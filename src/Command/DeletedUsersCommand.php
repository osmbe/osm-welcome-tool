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
use Symfony\Component\Stopwatch\Stopwatch;

#[AsCommand(
    name: 'osm:deleted-users',
    description: 'Download and cache OpenStreetMap deleted users',
)]
class DeletedUsersCommand extends Command
{
    final public const CACHE_KEY = 'users_deleted';
    private const CHUNK = 500;

    public function __construct(
        private readonly Stopwatch $stopwatch,
        private readonly EntityManagerInterface $entityManager,
        private readonly Filesystem $filesystem,
        private readonly CacheItemPoolInterface $cache,
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

        $this->getPerformance($io);

        return Command::SUCCESS;
    }

    /**
     * Download `users_deleted.txt` file.
     */
    protected function download(SymfonyStyle $io): string
    {
        $this->stopwatch->start('download');

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
                    int $bytes_max,
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

        $this->stopwatch->stop('download');

        return $path;
    }

    /**
     * Update cache.
     */
    private function updateCache(SymfonyStyle $io, string $path): CacheItemInterface
    {
        $this->stopwatch->start('update-cache');

        $usersDeletedCache = $this->cache->getItem(self::CACHE_KEY);

        $list = [];

        $resource = @fopen($path, 'r');
        if (false !== $resource) {
            while (($line = fgets($resource, 512)) !== false) {
                $list[] = (int) trim($line);
            }
            fclose($resource);
        }

        $usersDeletedCache->set($list);

        $this->cache->save($usersDeletedCache);

        $io->note(sprintf('%d deleted users', \count($list)));

        $this->stopwatch->stop('update-cache');

        return $usersDeletedCache;
    }

    /**
     * Remove deleted users from `mapper` and `user` tables.
     *
     * @param int[] $usersDeleted
     */
    private function delete(SymfonyStyle $io, array $usersDeleted): void
    {
        $this->stopwatch->start('delete');

        $io->text('Process...');

        $totalUsers = \count($usersDeleted);

        $progress = $io->createProgressBar($totalUsers);

        for ($i = 0; $i < $totalUsers; $i += self::CHUNK) {
            $chunk = \array_slice($usersDeleted, $i, self::CHUNK);

            // Clean `mapper` table
            $this->entityManager->createQuery('DELETE FROM App\Entity\Mapper m WHERE m.id IN (:id)')
                ->setParameter('id', $chunk)
                ->execute();

            // Clean `user` table
            $this->entityManager->createQuery('DELETE FROM App\Entity\User u WHERE u.id IN (:id)')
                ->setParameter('id', $chunk)
                ->execute();

            $progress->advance(\count($chunk));

            $this->stopwatch->lap('delete');

            // Clear the EntityManager to free memory
            $this->entityManager->clear();
        }

        $progress->finish();

        $this->stopwatch->stop('delete');
    }

    /**
     * Delete `users_deleted.txt` file.
     */
    private function clean(SymfonyStyle $io, string $path): void
    {
        $this->stopwatch->start('clean');

        $this->filesystem->remove($path);

        $io->info(sprintf('"%s" deleted!', $path));

        $this->stopwatch->start('clean');
    }

    private function getPerformance(SymfonyStyle $io): void
    {
        $perf = [
            ['Download', round($this->stopwatch->getEvent('download')->getDuration()), round($this->stopwatch->getEvent('download')->getMemory() / 1024 / 1024, 1)],
            ['Update cache', round($this->stopwatch->getEvent('update-cache')->getDuration()), round($this->stopwatch->getEvent('update-cache')->getMemory() / 1024 / 1024, 1)],
            ['Delete', round($this->stopwatch->getEvent('delete')->getDuration()), round($this->stopwatch->getEvent('delete')->getMemory() / 1024 / 1024, 1)],
            ['Clean-up', round($this->stopwatch->getEvent('clean')->getDuration()), round($this->stopwatch->getEvent('clean')->getMemory() / 1024 / 1024, 1)],
        ];

        $io->table(['Event', 'Duration (ms)', 'Memory (MB)'], $perf);
        $io->text(sprintf('Total: %.2f seconds - %.1f MB', array_sum(array_column($perf, 1)) / 1000, array_sum(array_column($perf, 2))));
    }
}
