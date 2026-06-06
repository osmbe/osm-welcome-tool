<?php

namespace App\Util;

final class DeletedUsersChunkReader
{
    /**
     * @return \Generator<int[], void, mixed, void>
     */
    public static function fromFile(string $path, int $chunkSize): \Generator
    {
        $file = new \SplFileObject($path, 'r');
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
        $file = new \SplFileObject($path, 'r');

        while (!$file->eof()) {
            if ('' !== trim($file->fgets())) {
                ++$count;
            }
        }

        return $count;
    }
}
