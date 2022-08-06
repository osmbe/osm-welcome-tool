<?php

namespace App\Command;

use App\Entity\Changeset;
use App\Entity\Mapper;
use App\Service\ChangesetProvider;
use App\Service\MapperProvider;
use App\Service\OpenStreetMapAPI;
use App\Service\OSMChaAPI;
use App\Service\RegionsProvider;
use Doctrine\ORM\EntityManagerInterface;
use ErrorException;
use SimpleXMLElement;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'osmcha:new-mapper',
    description: 'Get new mappers from OSMCha',
)]
class NewMapperCommand extends Command
{
    public function __construct(
        private ValidatorInterface $validator,
        private EntityManagerInterface $entityManager,
        private RegionsProvider $regionsProvider,
        private ChangesetProvider $changesetProvider,
        private MapperProvider $mapperProvider,
        private OSMChaAPI $osmcha,
        private OpenStreetMapAPI $osm
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('region', InputArgument::REQUIRED, 'Region')
            ->addOption('date', 'd', InputOption::VALUE_REQUIRED, 'Date used for filtering (format: YYYY-MM-DD)');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $validate = $this->validator->validate($input->getOption('date'), new Date());

        if ($validate->count() > 0) {
            throw new ErrorException($validate->get(0)->getMessage());
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $key = $input->getArgument('region');
        $date = $input->getOption('date');
        $region = $this->regionsProvider->getRegion($key);

        if (null === $region) {
            $io->error(sprintf('Region "%s" is not a valid key.', $key));

            return Command::FAILURE;
        }

        try {
            /** @var int[] */
            $usersId = [];
            /** @var int[] */
            $changesetsId = [];

            $changesetsResponse = $this->osmcha->getAreaOfInterestChangesets($region['osmcha.id']);

            $io->text(
                sprintf('%s %s', $changesetsResponse->getInfo('http_method'), $changesetsResponse->getInfo('url'))
            );

            $changesetsCollection = $changesetsResponse->toArray();

            $usersId = array_map(function (array $feature): int {
                return (int) $feature['properties']['uid'];
            }, $changesetsCollection['features']);
            $changesetsId = array_map(function (array $feature): int {
                return (int) $feature['id'];
            }, $changesetsCollection['features']);

            $getUsersResponse = $this->osm->getUsers($usersId);

            $io->text(sprintf('%s %s', $getUsersResponse->getInfo('http_method'), $getUsersResponse->getInfo('url')));

            $usersArray = $getUsersResponse->toArray();

            /** @var Mapper[] */
            $mappers = array_map(function (array $array) use ($key): Mapper {
                $mapper = $this->mapperProvider->fromOSM($array);
                $mapper->setRegion($key);

                return $mapper;
            }, $usersArray['users']);

            /** @var Changeset[] */
            $changesets = array_map(function (array $feature) use ($mappers): Changeset {
                $mapper = current(array_filter($mappers, function (Mapper $mapper) use ($feature): bool {
                    return $mapper->getId() === (int) $feature['properties']['uid'];
                }));

                $changeset = $this->changesetProvider->fromOSMCha($feature);
                $changeset->setMapper($mapper);

                return $changeset;
            }, $changesetsCollection['features']);

            foreach ($mappers as $mapper) {
                $firstChangeset = $this->getFirstChangeset($mapper, $io);

                /* @todo Add first changeset check date ?? */
                if (true === \in_array($firstChangeset->getId(), $changesetsId, true)) {
                    $this->entityManager->persist($mapper);

                    $mapperChangesets = array_filter($changesets, function (Changeset $changeset) use ($mapper): bool {
                        return $changeset->getMapper() === $mapper;
                    });
                    foreach ($mapperChangesets as $changeset) {
                        $this->entityManager->persist($changeset);
                    }

                    $this->entityManager->flush();

                    $io->success(
                        sprintf(
                            '[%s] %s : %s changeset(s)',
                            $firstChangeset->getCreatedAt()->format('r'),
                            $mapper->getDisplayName(),
                            \count($mapperChangesets)
                        )
                    );
                }
            }

            return Command::SUCCESS;
        } catch (ClientException $e) {
            $io->error($e->getMessage());
            $io->block($e->getResponse()->getContent(false));

            return Command::FAILURE;
        }
    }

    private function getFirstChangeset(Mapper $mapper, SymfonyStyle $io): Changeset
    {
        $response = $this->osm->getChangesetsByUser($mapper->getId());

        $io->text(sprintf('%s %s', $response->getInfo('http_method'), $response->getInfo('url')));

        $xml = new SimpleXMLElement($response->getContent());

        /** @var SimpleXMLElement[] */
        $changesetsElement = [];
        foreach ($xml->changeset as $changeset) {
            $changesetsElement[] = $changeset;
        }
        /** @var Changeset[] */
        $changesets = array_map(function (SimpleXMLElement $element): Changeset {
            return $this->changesetProvider->fromOSM($element);
        }, $changesetsElement);

        $createdAt = array_map(function (Changeset $changeset): int {
            return $changeset->getCreatedAt()->getTimestamp();
        }, $changesets);

        array_multisort($createdAt, \SORT_ASC, \SORT_NUMERIC, $changesets);

        return $changesets[0];
    }
}
