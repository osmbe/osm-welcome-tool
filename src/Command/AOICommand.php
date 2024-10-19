<?php

namespace App\Command;

use App\Service\OSMChaAPI;
use App\Service\RegionsProvider;
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
    name: 'osmcha:aoi',
    description: 'Create (or update) OSMCha Area of Interest',
)]
class AOICommand extends Command
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly RegionsProvider $provider,
        private readonly OSMChaAPI $api,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('region', InputArgument::REQUIRED, 'Region')
            ->addOption('create', 'c', InputOption::VALUE_NONE, 'Create Area of Interest')
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

        $key = $input->getArgument('region');
        $region = $this->provider->getRegion(null, $key);

        $name = sprintf('Welcome Tool for %s', $region['name']);
        $filters = [
            'geometry' => $this->provider->getGeometry($region['continent'], $region['key']),
            'all_reasons' => '40',
        ];

        $date = $input->getOption('date');
        if (null !== $date) {
            $filters['date__gte'] = $date;
        }

        try {
            if (true === $input->getOption('create')) {
                $response = $this->api->createAreaOfInterest($name, $filters);
            } else {
                $response = $this->api->updateAreaOfInterest($region['osmcha.id'], $name, $filters);
            }

            $io->text(sprintf('%s %s', $response->getInfo('http_method'), $response->getInfo('url')));

            $data = $response->toArray();

            if (null !== $date) {
                $io->success(sprintf('OSMCha Area of Interest identifier for "%s" is "%s" (%s).', $name, $data['id'], $data['properties']['filters']['date__gte']));
            } else {
                $io->success(sprintf('OSMCha Area of Interest identifier for "%s" is "%s".', $name, $data['id']));
            }

            return Command::SUCCESS;
        } catch (ClientException $e) {
            $io->error($e->getMessage());
            $io->block($e->getResponse()->getContent(false));

            return Command::FAILURE;
        }
    }
}
