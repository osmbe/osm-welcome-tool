<?php

namespace App\Command;

use App\Entity\Changeset;
use App\Entity\Mapper;
use App\Repository\ChangesetRepository;
use App\Repository\MapperRepository;
use App\Service\OpenStreetMapAPI;
use App\Service\OSMChaAPI;
use App\Service\RegionsProvider;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use SimpleXMLElement;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpClient\Exception\ClientException;

#[AsCommand(
    name: 'osmcha:new-mapper',
    description: 'Get new mappers from OSMCha',
)]
class NewMapperCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RegionsProvider $provider,
        private OSMChaAPI $osmcha,
        private OpenStreetMapAPI $osm
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('region', InputArgument::REQUIRED, 'Region')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $key = $input->getArgument('region');
        $region = $this->provider->getRegion($key);

        if (is_null($region)) {
            $io->error(sprintf('Region "%s" is not a valid key.', $key));

            return Command::FAILURE;
        }

        try {
            /** @var int[] */
            $usersId = [];

            $changesetsResponse = $this->osmcha->getAreaOfInterestChangesets($region['osmcha.id']);

            $io->text(sprintf('%s %s', $changesetsResponse->getInfo('http_method'), $changesetsResponse->getInfo('url')));

            $changesetsData = $changesetsResponse->toArray();

            foreach ($changesetsData['features'] as $feature) {
                $uid = $feature['properties']['uid'];

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

                    $this->createMapper($key, $user, $changeset, $io);
                }
            }

            return Command::SUCCESS;
        } catch (ClientException $e) {
            $io->error($e->getMessage());
            $io->block($e->getResponse()->getContent(false));

            return Command::FAILURE;
        }
    }

    private function createMapper(string $region, array $user, SimpleXMLElement $changeset, SymfonyStyle $io): void {
        /** @var MapperRepository */
        $mapperRepository = $this->entityManager->getRepository(Mapper::class);
        /** @var ChangesetRepository */
        $changesetRepository = $this->entityManager->getRepository(Changeset::class);

        $mapperEntity = $mapperRepository->find($user['user']['id']);
        if ($mapperEntity === null) {
            $mapperEntity = new Mapper();
            $mapperEntity->setId($user['user']['id']);
            $mapperEntity->setAccountCreated(new DateTime($user['user']['account_created']));
            $mapperEntity->setChangesetsCount($user['user']['changesets']['count']);
            $mapperEntity->setDisplayName($user['user']['display_name']);
            $mapperEntity->setLocale($changeset['properties']['metadata']['locale'] ?? null);
            $mapperEntity->setRegion($region);
            $mapperEntity->setStatus('new');

            $this->entityManager->persist($mapperEntity);
        }

        $changesetAttr = $changeset->attributes();

        $changesetEntity = $changesetRepository->find((int) $changesetAttr->id);
        if ($changesetEntity === null) {
            $extent = [
                floatval(self::extractTag($changeset->tag, 'min_lon')),
                floatval(self::extractTag($changeset->tag, 'min_lat')),
                floatval(self::extractTag($changeset->tag, 'max_lon')),
                floatval(self::extractTag($changeset->tag, 'max_lat')),
            ];

            $changesetEntity = new Changeset();
            $changesetEntity->setId((int) $changesetAttr->id);
            $changesetEntity->setMapper($mapperEntity);
            $changesetEntity->setComment(self::extractTag($changeset->tag, 'comment'));
            $changesetEntity->setEditor(self::extractTag($changeset->tag, 'created_by'));
            $changesetEntity->setChangesCount(intval($changesetAttr->changes_count));
            $changesetEntity->setExtent($extent);
            $changesetEntity->setTags([]);

            $this->entityManager->persist($changesetEntity);
        }

        $this->entityManager->flush();
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

            $changesets = [];
            foreach ($xml->changeset as $changeset) {
                $changesets[] = $changeset;
            }

            $createdAt = [];
            foreach ($changesets as $changeset) {
                $attr = $changeset->attributes;
                $createdAt[] = strtotime($attr['created_at']);
            }

            array_multisort($createdAt, SORT_ASC, SORT_NUMERIC, $changesets);

            return $changesets[0];
        } catch (Exception $e) {
            $io->warning($e->getMessage());

            return null;
        }
    }

    private static function extractTag(SimpleXMLElement $XMLTags, string $key): string | null {
        /** @var SimpleXMLElement[] */
        $tags = [];
        foreach ($XMLTags as $XMLTag) {
            $tags[] = $XMLTag;
        }
        $filter = array_filter($tags, function (SimpleXMLElement $tag) use ($key) {
            $attr = $tag->attributes();
            return (string) $attr->k === $key;
        });

        if (count($filter) === 0) {
            return null;
        }

        $tag = current($filter);
        $attr = $tag->attributes();

        return (string) $attr->v;
    }
}
