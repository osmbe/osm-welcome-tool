<?php

namespace App\Command;

use App\Entity\Region;
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
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'welcome:update',
    description: 'Update command for all regions for the day before today',
)]
class UpdateCommand extends Command
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly RegionsProvider $provider,
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('continent', InputArgument::REQUIRED, 'Continent')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force process (even if it has already been processed today)');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $validate = $this->validator->validate($input->getArgument('continent'), new Callback(function (string $value, ExecutionContextInterface $context) {
            $regions = $this->provider->getRegions();

            if (!isset($regions[$value]) || 0 === \count($regions[$value])) {
                $context
                    ->buildViolation(sprintf('Continent "%s" does not exist.', $value))
                    ->addViolation();
            }
        }));

        if ($validate->count() > 0) {
            throw new \ErrorException($validate->get(0)->getMessage());
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $continent = $input->getArgument('continent');

        $lastUpdate = [];
        $regions = $this->provider->getRegions();

        foreach ($regions[$continent] as $key => $region) {
            /** @var Region|null */
            $r = $this->entityManager->find(Region::class, $key);
            $lastUpdate = null === $r ? null : $r->getLastUpdate();

            $io->title(sprintf('%s (%s)', $region['name'], date('Y-m-d')));

            if (true === $input->getOption('force') || null === $lastUpdate || $lastUpdate->format('Y-m-d') < date('Y-m-d')) {
                if (null === $lastUpdate) {
                    // If there never was an update, get new mappers from the last 5 days
                    $date = (new \DateTime())->sub(new \DateInterval('P5D'))->format('Y-m-d');
                    $io->note(sprintf('Cache is not set, get new mappers from %s.', $date));
                } elseif (true === $input->getOption('force') && $lastUpdate->format('Y-m-d') === date('Y-m-d')) {
                    // If last update was today and process is forced, get new mappers from yesterday
                    $date = $lastUpdate->sub(new \DateInterval('P1D'))->format('Y-m-d');
                    $io->note(sprintf('Get new mappers from %s (forced).', $date));
                } else {
                    // Get new mappers from the last update date
                    $date = $lastUpdate->format('Y-m-d');
                    $io->note(sprintf('Get new mappers from %s.', $date));
                }

                try {
                    $this->process($key, $date, $output);
                } catch (\Exception $e) {
                    $io->error($e->getMessage());
                }
            } else {
                $io->note('Skip, already processed.');
            }
        }

        return Command::SUCCESS;
    }

    private function process(string $region, string $date, OutputInterface $output): void
    {
        $newMapperCommand = $this->getApplication()->find('osmcha:new-mapper');
        $newMapperCommand->run(new ArrayInput(['region' => $region, '-d' => $date]), $output);
    }
}
