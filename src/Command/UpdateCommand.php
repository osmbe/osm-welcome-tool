<?php

namespace App\Command;

use App\Service\RegionsProvider;
use DateInterval;
use DateTime;
use Symfony\Component\Cache\Adapter\AdapterInterface;
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
        private AdapterInterface $cache
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force process (even if it has already been processed today)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);


        $lastUpdate = [];
        $regions = $this->provider->getRegions();

        foreach ($regions as $key => $region) {
            $cacheKey = sprintf('last_update.%s', $key);

            $io->title(sprintf('%s (%s)', $region['name'], date('Y-m-d')));

            $lastUpdate = $this->cache->getItem($cacheKey);
            if ($input->getOption('force') === true || !$lastUpdate->isHit() || $lastUpdate->get() < date('Y-m-d')) {
                $date = (new DateTime())->sub(new DateInterval('P1D'))->format('Y-m-d');

                $this->process($key, $date, $output);

                $lastUpdate->set(date('Y-m-d'));
                $this->cache->save($lastUpdate);
            } else {
                $io->info('Skip, already processed.');
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
