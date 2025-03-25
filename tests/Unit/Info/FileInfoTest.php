<?php

namespace Tests\Upload\Unit\Info;

use PHPUnit\Framework\TestCase;
use Vista\Upload\Info\FileInfo;
use Vista\Upload\Info\Info;

class FileInfoTest extends TestCase
{
    private Info $fileInfo;

    public function setUp(): void
    {
        parent::setUp();
        $this->fileInfo = new FileInfo([
            'name' => 'file.txt',
            'type' => 'text/plain',
            'tmp_name' => '/tmp/php/php1h4j1o',
            'error' => 0,
            'size' => 123,
        ]);
    }

    public function testGetFileName(): void
    {
        $this->assertSame('file.txt', $this->fileInfo->getFilename());
    }

    public function testGetExtension(): void
    {
        $this->assertSame('txt', $this->fileInfo->getExtension());
    }

    public function testGetMimeType(): void
    {
        $this->assertSame('text/plain', $this->fileInfo->getMimeType());
    }

    public function testGetSize(): void
    {
        $this->assertSame(123, $this->fileInfo->getSize());
    }

    public function testGetTmpName(): void
    {
        $this->assertSame('/tmp/php/php1h4j1o', $this->fileInfo->getTmpName());
    }

    public function testGetError(): void
    {
        $this->assertSame(0, $this->fileInfo->getError());
    }

    public function testGetFileNameWithEmptyArray(): void
    {
        $fileInfo = new FileInfo([]);
        $this->assertSame('', $fileInfo->getFilename());
    }

    public function testGetMimeTypeWithEmptyArray(): void
    {
        $fileInfo = new FileInfo([]);
        $this->assertSame('', $fileInfo->getMimeType());
    }

    public function testGetSizeWithEmptyArray(): void
    {
        $fileInfo = new FileInfo([]);
        $this->assertSame(0, $fileInfo->getSize());
    }

    public function testGetTmpNameWithEmptyArray(): void
    {
        $fileInfo = new FileInfo([]);
        $this->assertSame('', $fileInfo->getTmpName());
    }

    public function testGetErrorWithEmptyArray(): void
    {
        $fileInfo = new FileInfo([]);
        $this->assertSame(0, $fileInfo->getError());
    }
}
