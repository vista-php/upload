<?php

namespace Tests\Upload\Unit\System;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Vista\Upload\System\FileSystem;

class FileSystemTest extends TestCase
{
    public function testExists(): void
    {
        $fileSystem = new FileSystem();

        $this->assertTrue($fileSystem->exists(__FILE__));
    }

    public function testExistsReturnsFalse(): void
    {
        $fileSystem = new FileSystem();

        $this->assertFalse($fileSystem->exists('non-existent-file'));
    }

    public function testDelete(): void
    {
        $fileSystem = m::mock(FileSystem::class)->makePartial();
        $fileSystem->shouldReceive('delete')->once()
            ->with('file.php')
            ->andReturn(true);

        $this->assertTrue($fileSystem->delete('file.php'));
    }

    public function testMoveUploadedFile(): void
    {
        $fileSystem = m::mock(FileSystem::class)->makePartial();
        $fileSystem->shouldReceive('moveUploadedFile')->once()
            ->with('tmp/file.php', 'new/file.php')
            ->andReturn(true);

        $this->assertTrue($fileSystem->moveUploadedFile('tmp/file.php', 'new/file.php'));
    }
}
