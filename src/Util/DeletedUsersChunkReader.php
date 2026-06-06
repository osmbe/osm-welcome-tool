<?php

namespace App\Util;

final class DeletedUsersChunkReader
{
    /**
     * @return \Generator<int[], void, mixed, void>
     */
    public static function fromFile(string $path, int $chunkSize): \Generator
    {
        $file = self::openFile($path);
        $chunk = [];

        while (!$file->eof()) {
            $line = trim($file->fgets());

            if ('' === $line) {
                continue;
            }

            $chunk[] = (int) $line;

            if (\count($chunk) === $chunkSize) {
                yield $chunk;
                $chunk = [];
            }
        }

        if ([] !== $chunk) {
            yield $chunk;
        }
    }

    public static function count(string $path): int
    {
        $count = 0;
        $file = self::openFile($path);

        while (!$file->eof()) {
            if ('' !== trim($file->fgets())) {
                ++$count;
            }
        }

        return $count;
    }

    private static function openFile(string $path): \SplFileObject
    {
        try {
            return new \SplFileObject($path, 'r');
        } catch (\RuntimeException $exception) {
            throw new \RuntimeException(\sprintf('Unable to read deleted users file "%s".', $path), 0, $exception);
        }
    }
}
