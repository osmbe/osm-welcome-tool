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
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'osmcha:new-mapper',
    description: 'Get new mappers from OSMCha',
)]
class NewMapperCommand extends Command
{
    public function __construct(
        private readonly Stopwatch $stopwatch,
        private readonly ValidatorInterface $validator,
        private readonly EntityManagerInterface $entityManager,
        private readonly RegionsProvider $regionsProvider,
        private readonly ChangesetProvider $changesetProvider,
        private readonly MapperProvider $mapperProvider,
        private readonly OSMChaAPI $osmcha,
        private readonly OpenStreetMapAPI $osm,
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
        $io = new SymfonyStyle($input, $output);

        $this->stopwatch->start('initialize-region');

        $key = $input->getArgument('region');

        $config = $this->regionsProvider->getRegion(null, $key);
        $region = $this->regionsProvider->getEntity($key);

        $this->stopwatch->stop('initialize-region');

        $this->stopwatch->start('initialize-date');

        $date = $input->getOption('date');

        if (null === $date) {
            if (null === $region) {
                $region = new Region();
                $region->setId($key);

                // If there never was an update, get new mappers from the last 5 days
                $date = (new \DateTime())->sub(new \DateInterval('P5D'))->format('Y-m-d');
                $io->info(sprintf('Region was not processed yet, get new mappers from %s.', $date));
            } else {
                $date = $region->getLastUpdate()->sub(new \DateInterval('P1D'))->format('Y-m-d');
            }
        } elseif (null === $region) {
            $region = new Region();
            $region->setId($key);
        }

        $this->stopwatch->stop('initialize-date');

        $this->stopwatch->start('update-aoi');

        $io->title(sprintf('Update the Area of Interest "%s" (%s)', $key, $date));

        $aoiCommand = $this->getApplication()->find('osmcha:aoi');
        $aoiCommand->run(new ArrayInput([
            'region' => $key,
            '-d' => $date,
        ]), $output);

        $this->stopwatch->stop('update-aoi');

        $this->stopwatch->start('process');

        $io->title(sprintf('Get new mappers from Area of Interest "%s"', $key));

        try {
            /** @var int[] $usersId */
            $usersId = [];

            $changesets = $this->getNewChangesets($config, $usersId, $io);

            for ($i = 0; $i < \count($usersId); ++$i) {
                try {
                    $mapper = $this->entityManager->find(Mapper::class, $usersId[$i]);
                    if (null === $mapper) {
                        $mapper = $this->getMapper($usersId[$i], $io);
                        $firstChangeset = $this->getFirstChangeset($usersId[$i], $io);

                        if (true === \in_array($firstChangeset->getId(), array_column($changesets, 'id'), true)) {
                            $mapper->addRegion($region);

                            $mapperChangesets = array_filter($changesets, function (array $changeset) use ($mapper) { return (int) $changeset['properties']['uid'] === $mapper->getId(); });
                            foreach ($mapperChangesets as $changeset) {
                                $changeset = $this->changesetProvider->fromOSMCha($changeset);

                                $mapper->addChangeset($changeset);

                                $this->entityManager->persist($changeset);
                            }

                            $this->entityManager->persist($mapper);

                            $io->info(sprintf('Mapper %s (%d) added with %d changeset(s) (first changeset: %d)', $mapper->getDisplayName(), $mapper->getId(), count($mapperChangesets), $firstChangeset->getId()));
                        }
                    } else {
                        $io->note(sprintf('Mapper #%d already exists', $usersId[$i]));
                    }
                } catch (\Exception $e) {
                    $io->error($e->getMessage());
                    $io->block($e->getTraceAsString());
                }

                $this->stopwatch->lap('process');
            }

            $region->setLastUpdate(new \DateTime());

            $this->entityManager->persist($region);

            $this->entityManager->flush();

            $this->stopwatch->stop('process');

            $this->getPerformance($io);

            return Command::SUCCESS;
        } catch (ClientException $e) {
            $io->error($e->getMessage());
            $io->block($e->getResponse()->getContent(false));

            return Command::FAILURE;
        }
    }

    private function getNewChangesets(array $region, array &$users, SymfonyStyle $io): array
    {
        $response = $this->osmcha->getAreaOfInterestChangesets($region['osmcha.id']);

        // $io->text(sprintf('%s %s', $response->getInfo('http_method'), $response->getInfo('url')));

        $geojson = $response->toArray();
        $features = $geojson['features'];

        $users = array_values(array_unique(array_map(fn (array $feature) => (int) $feature['properties']['uid'], $features), \SORT_NUMERIC));

        $io->success(sprintf('Found %d new changeset(s) from %d new user(s)', \count($features), \count($users)));

        return $features;
    }

    private function getFirstChangeset(int $userId, SymfonyStyle $io): Changeset
    {
        $response = $this->osm->getChangesetsByUser($userId);

        // $io->text(sprintf('%s %s', $response->getInfo('http_method'), $response->getInfo('url')));

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

    private function getMapper(int $userId, SymfonyStyle $io): Mapper
    {
        $response = $this->osm->getUsers([$userId]);

        // $io->text(sprintf('%s %s', $response->getInfo('http_method'), $response->getInfo('url')));

        $response = $response->toArray(true);

        $users = $response['users'];

        if (0 === \count($users)) {
            throw new \InvalidArgumentException(sprintf('User #%d not found', $userId));
        }

        $mapper = $this->mapperProvider->fromOSM($users[0]);

        return $mapper;
    }

    private function getPerformance(SymfonyStyle $io): void
    {
        $perf = [
            ['Initialize region', round($this->stopwatch->getEvent('initialize-region')->getDuration()), round($this->stopwatch->getEvent('initialize-region')->getMemory() / 1024 / 1024, 1)],
            ['Initialize date', round($this->stopwatch->getEvent('initialize-date')->getDuration()), round($this->stopwatch->getEvent('initialize-date')->getMemory() / 1024 / 1024, 1)],
            ['Update AOI', round($this->stopwatch->getEvent('update-aoi')->getDuration()), round($this->stopwatch->getEvent('update-aoi')->getMemory() / 1024 / 1024, 1)],
            ['Process', round($this->stopwatch->getEvent('process')->getDuration()), round($this->stopwatch->getEvent('process')->getMemory() / 1024 / 1024, 1)],
        ];

        $io->table(['Event', 'Duration (ms)', 'Memory (MB)'], $perf);
        $io->text(sprintf('Total: %.2f seconds - %.1f MB', array_sum(array_column($perf, 1)) / 1000, array_sum(array_column($perf, 2))));
    }
}
