<?php

namespace App\Command;

use App\Service\ChangesetProvider;
use App\Service\MapperProvider;
use App\Service\OpenStreetMapAPI;
use App\Service\OSMChaAPI;
use App\Service\RegionsProvider;
use Doctrine\ORM\EntityManagerInterface;
use ErrorException;
use Exception;
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
            ->addOption('date', 'd', InputOption::VALUE_REQUIRED, 'Date used for filtering (format: YYYY-MM-DD)')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output) {
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

        if (is_null($region)) {
            $io->error(sprintf('Region "%s" is not a valid key.', $key));

            return Command::FAILURE;
        }

        try {
            /** @var int[] */
            $usersId = [];
            /** @var int[] */
            $changesetsId = [];

            $changesetsResponse = $this->osmcha->getAreaOfInterestChangesets($region['osmcha.id']);

            $io->text(sprintf('%s %s', $changesetsResponse->getInfo('http_method'), $changesetsResponse->getInfo('url')));

            $changesetsData = $changesetsResponse->toArray();
            // var_dump($changesetsData);

            foreach ($changesetsData['features'] as $feature) {
                $id = $feature['id'];
                $uid = $feature['properties']['uid'];

                if (!in_array($id, $changesetsId, true)) {
                    $changesetsId[] = $id;
                }
                if (!in_array($uid, $usersId, true)) {
                    $usersId[] = $uid;
                }
            }

            $getUsersResponse = $this->osm->getUsers($usersId);

            $io->text(sprintf('%s %s', $getUsersResponse->getInfo('http_method'), $getUsersResponse->getInfo('url')));

            $users = $getUsersResponse->toArray();

            foreach($usersId as $uid) {
                $changeset = $this->getFirstChangeset($uid, $io);
                if (!is_null($changeset)) {
                    $_users = array_filter($users['users'], function ($u) use ($uid) { return intval($u['user']['id']) === intval($uid); });
                    $user = current($_users);

                    $mapper = $this->mapperProvider->fromOSM($user);
                    $mapper->setRegion($key);

                    $changeset = $this->changesetProvider->fromOSM($changeset);
                    $changeset->setMapper($mapper);

                    if (in_array($changeset->getId(), $changesetsId, true) && (is_null($date) || (!is_null($date) && $date <= $changeset->getCreatedAt()->format('Y-m-d')))) {
                        $io->success(sprintf('%s %s', $mapper->getDisplayName(), $changeset->getCreatedAt()->format('c')));

                        $this->entityManager->persist($mapper);
                        $this->entityManager->persist($changeset);
                        $this->entityManager->flush();
                    }
                }
            }

            return Command::SUCCESS;
        } catch (ClientException $e) {
            $io->error($e->getMessage());
            $io->block($e->getResponse()->getContent(false));

            return Command::FAILURE;
        }
    }

    private function getFirstChangeset(int $uid, SymfonyStyle $io): SimpleXMLElement | null
    {
        try {
            // $response = $this->osmcha->getChangesets([
            //     'uids' => $uid,
            //     'order_by' => 'date',
            //     'page_size' => 1,
            // ]);

            // $io->text(sprintf('%s %s', $response->getInfo('http_method'), $response->getInfo('url')));

            // $data = $response->toArray();

            // return $data['features'][0];

            $response = $this->osm->getChangesetsByUser($uid);

            $io->text(sprintf('%s %s', $response->getInfo('http_method'), $response->getInfo('url')));

            $xml = new SimpleXMLElement($response->getContent());

            /** @var SimpleXMLElement[] */
            $changesets = [];
            foreach ($xml->changeset as $changeset) {
                $changesets[] = $changeset;
            }

            /** @var int[] */
            $createdAt = [];
            foreach ($changesets as $changeset) {
                $attr = $changeset->attributes();
                $createdAt[] = strtotime($attr['created_at']);
            }

            array_multisort($createdAt, SORT_ASC, SORT_NUMERIC, $changesets);

            // if ($uid === 12491507) {
            //     var_dump($changesets[0]);
            //     var_dump(current($changesets));
            // }

            return $changesets[0];
        } catch (Exception $e) {
            $io->warning($e->getMessage());

            return null;
        }
    }
}
