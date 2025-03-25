<?php

namespace Tests\Upload\Unit\System;

use PHPUnit\Framework\TestCase;
use Vista\Upload\System\FileSystem;
use Vista\Upload\System\FileSystemFactory;

class FileSystemFactoryTest extends TestCase
{
    public function testCreateNew(): void
    {
        $factory = new FileSystemFactory();

        $this->assertInstanceOf(FileSystem::class, $factory->create());
    }

    public function testCreateWithSystem(): void
    {
        $system = new FileSystem();
        $factory = new FileSystemFactory($system);

        $this->assertSame($system, $factory->create());
    }
}
