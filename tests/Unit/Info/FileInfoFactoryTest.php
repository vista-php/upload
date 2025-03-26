<?php

namespace Tests\Upload\Unit\Info;

use PHPUnit\Framework\TestCase;
use Vista\Upload\Info\FileInfo;
use Vista\Upload\Info\FileInfoFactory;

class FileInfoFactoryTest extends TestCase
{
    public function testCreateNew(): void
    {
        $factory = new FileInfoFactory();
        $_FILES['file'] = [];
        $this->assertInstanceOf(FileInfo::class, $factory->create('file'));
    }

    public function testCreateNewWithoutFiles(): void
    {
        $factory = new FileInfoFactory();
        $this->assertInstanceOf(FileInfo::class, $factory->create('file'));
    }

    public function testCreateWithSystem(): void
    {
        $info = new FileInfo([]);
        $factory = new FileInfoFactory($info);

        $this->assertSame($info, $factory->create('file'));
    }
}
