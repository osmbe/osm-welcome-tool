<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'osm:deleted-users',
    description: 'Download OpenStreetMap deleted users',
)]
class DeletedUsersCommand extends Command
{
    final public const CACHE_KEY = 'users_deleted';
    private const CHUNK = 250000;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Filesystem $filesystem,
        private readonly CacheItemPoolInterface $cache
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $path = $this->download($io);

        $usersDeletedCache = $this->updateCache($io, $path);

        $this->delete($io, $usersDeletedCache->get());

        $this->clean($io, $path);

        return Command::SUCCESS;
    }

    /**
     * Download `users_deleted.txt` file.
     */
    protected function download(SymfonyStyle $io): string
    {
        $progress = $io->createProgressBar();

        $context = stream_context_create(
            [],
            [
                'notification' => function (
                    int $notification_code,
                    int $severity,
                    ?string $message,
                    int $message_code,
                    int $bytes_transferred,
                    int $bytes_max
                ) use ($io, $progress) {
                    switch ($notification_code) {
                        case \STREAM_NOTIFY_RESOLVE:
                        case \STREAM_NOTIFY_AUTH_REQUIRED:
                        case \STREAM_NOTIFY_MIME_TYPE_IS:
                        case \STREAM_NOTIFY_COMPLETED:
                        case \STREAM_NOTIFY_FAILURE:
                        case \STREAM_NOTIFY_AUTH_RESULT:
                            break;

                        case \STREAM_NOTIFY_REDIRECTED:
                            $io->text(sprintf('Being redirected to: %s', $message));
                            break;

                        case \STREAM_NOTIFY_CONNECT:
                            $io->text('Connected...');
                            break;

                        case \STREAM_NOTIFY_FILE_SIZE_IS:
                            $progress->start($bytes_max);
                            break;

                        case \STREAM_NOTIFY_PROGRESS:
                            $progress->setProgress($bytes_transferred);
                            break;
                    }
                },
            ]
        );

        $content = file_get_contents('https://planet.openstreetmap.org/users_deleted/users_deleted.txt', false, $context);

        $progress->finish();
        $io->newLine();

        $path = $this->filesystem->tempnam(sys_get_temp_dir(), 'users_deleted_', '.txt');

        $this->filesystem->dumpFile($path, $content);

        $io->info(sprintf('Saved to "%s"!', $path));

        return $path;
    }

    /**
     * Update cache.
     */
    private function updateCache(SymfonyStyle $io, string $path): CacheItemInterface
    {
        $usersDeletedCache = $this->cache->getItem(self::CACHE_KEY);

        $list = array_map(fn ($line) => (int) trim($line), file($path, \FILE_IGNORE_NEW_LINES | \FILE_SKIP_EMPTY_LINES));

        $usersDeletedCache->set($list);

        $this->cache->save($usersDeletedCache);

        $io->note(sprintf('Deleted users: %d', count($list)));

        return $usersDeletedCache;
    }

    /**
     * Remove deleted users from `mapper` and `user` tables.
     */
    private function delete(SymfonyStyle $io, array $usersDeleted): void
    {
        $io->text('Process...');

        $chunks = array_chunk($usersDeleted, self::CHUNK);

        $progress = $io->createProgressBar(count($usersDeleted));

        foreach ($chunks as $i => $chunk) {
            // Clean `mapper` table
            $this->entityManager->createQuery('DELETE FROM App\Entity\Mapper m WHERE m.id IN (:id)')
                ->setParameter('id', $chunk)
                ->execute();

            // Clean `user` table
            $this->entityManager->createQuery('DELETE FROM App\Entity\User u WHERE u.id IN (:id)')
                ->setParameter('id', $chunk)
                ->execute();

            $progress->advance(count($chunk));
        }

        $progress->finish();
    }

    /**
     * Delete `users_deleted.txt` file.
     */
    private function clean(SymfonyStyle $io, string $path): void
    {
        $this->filesystem->remove($path);

        $io->info(sprintf('"%s" deleted!', $path));
    }
}
