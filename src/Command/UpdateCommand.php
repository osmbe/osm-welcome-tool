<?php

namespace App\Command;

use App\Service\RegionsProvider;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'welcome:update',
    description: 'Update command for all regions for the day before today',
)]
class UpdateCommand extends Command
{
    public function __construct(
        private RegionsProvider $provider,
        private CacheItemPoolInterface $cache
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(
            'force',
            'f',
            InputOption::VALUE_NONE,
            'Force process (even if it has already been processed today)'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $lastUpdate = [];
        $regions = $this->provider->getRegions();

        foreach ($regions as $continent => $group) {
            foreach ($group as $key => $region) {
                $cacheKey = sprintf('last_update.%s', $key);

                $io->title(sprintf('%s (%s)', $region['name'], date('Y-m-d')));

                $lastUpdate = $this->cache->getItem($cacheKey);
                if (true === $input->getOption('force') || !$lastUpdate->isHit() || $lastUpdate->get() < date('Y-m-d')) {
                    if (!$lastUpdate->isHit()) {
                        // If cache is not set, get new mappers from the last 5 days
                        $date = (new \DateTime())->sub(new \DateInterval('P5D'))->format('Y-m-d');
                        $io->note(sprintf('Cache is not set, get new mappers from %s.', $date));
                    } elseif (true === $input->getOption('force') && $lastUpdate->get() === date('Y-m-d')) {
                        // If last update was today and process is forced, get new mappers from yesterday
                        $date = (new \DateTime($lastUpdate->get()))->sub(new \DateInterval('P1D'))->format('Y-m-d');
                        $io->note(sprintf('Get new mappers from %s (forced).', $date));
                    } else {
                        // Get new mappers from the last update date
                        $date = (new \DateTime($lastUpdate->get()))->format('Y-m-d');
                        $io->note(sprintf('Get new mappers from %s.', $date));
                    }

                    try {
                        $this->process($key, $date, $output);

                        $lastUpdate->set(date('c'));
                        $this->cache->save($lastUpdate);
                    } catch (\Exception $e) {
                        $io->error($e->getMessage());
                    }
                } else {
                    $io->note('Skip, already processed.');
                }
            }
        }

        return Command::SUCCESS;
    }

    private function process(string $region, string $date, OutputInterface $output): void
    {
        $aoiCommand = $this->getApplication()->find('osmcha:aoi');
        $aoiCommand->run(new ArrayInput([
            'region' => $region,
            '-d' => $date,
        ]), $output);

        $newMapperCommand = $this->getApplication()->find('osmcha:new-mapper');
        $newMapperCommand->run(new ArrayInput([
            'region' => $region,
            '-d' => $date,
        ]), $output);
    }
}
