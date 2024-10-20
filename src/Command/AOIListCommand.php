<?php

namespace App\Command;

use App\Service\OSMChaAPI;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'osmcha:aoi-list',
    description: 'Get list of OSMCha Area of Interest',
)]
class AOIListCommand extends Command
{
    public function __construct(
        private readonly OSMChaAPI $osmcha,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $page = 1;

        $content = $this->osmcha->listAreasOfInterest('name', $page++)->toArray();
        $count = $content['count'];
        $features = $content['results']['features'];

        while (null !== $content['next']) {
            $content = $this->osmcha->listAreasOfInterest('name', $page++)->toArray();
            $features = array_merge($features, $content['results']['features']);
        }

        $io->info(sprintf('Found %d Area(s) of Interest.', $count));

        $welcome = array_filter($features, fn ($feature) => 'Welcome Tool' === substr($feature['properties']['name'], 0, 12));

        $table = array_map(fn ($feature) => [
            $feature['id'],
            $feature['properties']['name'],
            $feature['properties']['date'],
            $feature['properties']['filters']['date__gte'] ?? null,
            $feature['properties']['filters']['all_reasons'] ?? null,
        ], $welcome);

        $io->table([
            'ID',
            'Name',
            'Date',
            'Filter (Date)',
            'Filter (Reasons)',
        ], $table);

        return Command::SUCCESS;
    }
}
