<?php

namespace App\Command;

use App\Service\RegionsProvider;
use DateInterval;
use DateTime;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
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
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $lastUpdate = [];
        $regions = $this->provider->getRegions();

        foreach ($regions as $key => $region) {
            $date = (new DateTime())->sub(new DateInterval('P1D'))->format('Y-m-d');

            $io->title(sprintf('%s (%s)', $region['name'], $date));

            $aoiCommand = $this->getApplication()->find('osmcha:aoi');
            $aoiCommand->run(new ArrayInput([
                'region' => $key,
                '-d' => $date,
            ]), $output);

            $newMapperCommand = $this->getApplication()->find('osmcha:new-mapper');
            $newMapperCommand->run(new ArrayInput([
                'region' => $key,
                '-d' => $date,
            ]), $output);

            $lastUpdate[$key] = $date;
        }

        $cache = new FilesystemAdapter('welcome');
        $lastUpdateCache = $cache->getItem('last_update');
        $lastUpdateCache->set($lastUpdate);
        $cache->save($lastUpdateCache);

        return Command::SUCCESS;
    }
}
