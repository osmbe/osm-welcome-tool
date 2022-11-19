<?php

namespace App\Command;

use App\Entity\Changeset;
use App\Entity\Mapper;
use App\Entity\Region;
use App\Service\ChangesetProvider;
use App\Service\MapperProvider;
use App\Service\OpenStreetMapAPI;
use App\Service\OSMChaAPI;
use App\Service\RegionsProvider;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
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
        private readonly ValidatorInterface $validator,
        private readonly EntityManagerInterface $entityManager,
        private readonly RegionsProvider $regionsProvider,
        private readonly ChangesetProvider $changesetProvider,
        private readonly MapperProvider $mapperProvider,
        private readonly OSMChaAPI $osmcha,
        private readonly OpenStreetMapAPI $osm,
        private readonly CacheItemPoolInterface $cache
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
            throw new \ErrorException($validate->get(0)->getMessage());
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $usersDeletedCache = $this->cache->getItem(DeletedUsersCommand::CACHE_KEY);
        if (!$usersDeletedCache->isHit()) {
            $deletedUsersCommand = $this->getApplication()->find('osm:deleted-users');
            $deletedUsersCommand->run(new ArrayInput([]), $output);

            $usersDeletedCache = $this->cache->getItem(DeletedUsersCommand::CACHE_KEY);
        }

        $usersDeleted = $usersDeletedCache->get();

        $io = new SymfonyStyle($input, $output);

        $key = $input->getArgument('region');
        $date = $input->getOption('date');
        $region = $this->regionsProvider->getRegion(null, $key);

        if (null === $region) {
            $io->error(sprintf('Region "%s" is not a valid key.', $key));

            return Command::FAILURE;
        }

        if (!is_null($date)) {
            $aoiCommand = $this->getApplication()->find('osmcha:aoi');
            $aoiCommand->run(new ArrayInput([
                'region' => $key,
                '-d' => $date,
            ]), $output);
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

            $features = array_filter($changesetsCollection['features'], fn (array $feature) => !\in_array((int) $feature['properties']['uid'], $usersDeleted, true));

            $usersId = array_map(fn (array $feature) => (int) $feature['properties']['uid'], $features);

            /** @var Mapper[] */
            $mappers = [];
            $usersIdChunks = array_chunk($usersId, 50);
            foreach ($usersIdChunks as $i => $chunk) {
                $mappers = array_merge($mappers, $this->getUsers($key, $chunk, $io));
            }

            $changesetsId = array_map(fn (array $feature) => (int) $feature['id'], $features);

            /** @var Changeset[] */
            $changesets = array_map(function (array $feature) use ($mappers): Changeset {
                $mapper = current(array_filter($mappers, fn (Mapper $mapper): bool => $mapper->getId() === (int) $feature['properties']['uid']));

                $changeset = $this->changesetProvider->fromOSMCha($feature);
                $changeset->setMapper($mapper);

                return $changeset;
            }, $features);

            foreach ($mappers as $mapper) {
                $firstChangeset = $this->getFirstChangeset($mapper, $io);

                /* @todo Add first changeset check date ?? */
                if (true === \in_array($firstChangeset->getId(), $changesetsId, true)) {
                    if (null === $this->entityManager->find(Mapper::class, $mapper->getId())) {
                        $this->entityManager->persist($mapper);
                    }

                    $mapperChangesets = array_filter($changesets, fn (Changeset $changeset): bool => $changeset->getMapper() === $mapper);
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

            /** @var Region|null */
            $r = $this->entityManager->find(Region::class, $key);
            if (null === $r) {
                $r = new Region();
                $r->setId($key);
            }
            $r->setLastUpdate(new \DateTime());
            $this->entityManager->persist($r);
            $this->entityManager->flush();

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

        $xml = new \SimpleXMLElement($response->getContent());

        /** @var \SimpleXMLElement[] */
        $changesetsElement = [];
        foreach ($xml->changeset as $changeset) {
            $changesetsElement[] = $changeset;
        }
        /** @var Changeset[] */
        $changesets = array_map(fn (\SimpleXMLElement $element): Changeset => $this->changesetProvider->fromOSM($element), $changesetsElement);

        $createdAt = array_map(fn (Changeset $changeset): int => $changeset->getCreatedAt()->getTimestamp(), $changesets);

        array_multisort($createdAt, \SORT_ASC, \SORT_NUMERIC, $changesets);

        return $changesets[0];
    }

    private function getUsers(string $key, array $ids, SymfonyStyle $io): array
    {
        try {
            $getUsersResponse = $this->osm->getUsers($ids);

            $io->text(sprintf('%s %s', $getUsersResponse->getInfo('http_method'), $getUsersResponse->getInfo('url')));

            $usersArray = $getUsersResponse->toArray();

            /** @var Mapper[] */
            $mappers = array_map(function (array $array) use ($key): Mapper {
                $region = $this->regionsProvider->getEntity($key);

                if (null === $region) {
                    $region = new Region();
                    $region->setId($key);
                    $region->setLastUpdate(new \DateTime('1970-01-01'));

                    $this->entityManager->persist($region);
                }

                $mapper = $this->mapperProvider->fromOSM($array);
                $mapper->addRegion($region);

                return $mapper;
            }, $usersArray['users']);

            return $mappers;
        } catch (\Exception $exception) {
            $io->warning($exception->getMessage());

            return [];
        }
    }
}
