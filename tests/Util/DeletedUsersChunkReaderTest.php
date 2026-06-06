<?php

require_once dirname(__DIR__, 2).'/src/Util/DeletedUsersChunkReader.php';

use App\Util\DeletedUsersChunkReader;
use PHPUnit\Framework\TestCase;

final class DeletedUsersChunkReaderTest extends TestCase
{
    public function testItCountsAndChunksDeletedUsersWithoutKeepingTheWholeListInMemory(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'deleted_users_test_');

        self::assertNotFalse($path);

        file_put_contents($path, "1\n2\n3\n\n4\n5\n");

        self::assertSame(5, DeletedUsersChunkReader::count($path));
        self::assertSame([[1, 2], [3, 4], [5]], iterator_to_array(DeletedUsersChunkReader::fromFile($path, 2), false));

        unlink($path);
    }

    public function testItRaisesADescriptiveExceptionForMissingFiles(): void
    {
        $path = sys_get_temp_dir().'/deleted_users_missing_'.uniqid('', true).'.txt';

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(sprintf('Unable to read deleted users file "%s".', $path));

        DeletedUsersChunkReader::count($path);
    }
}
