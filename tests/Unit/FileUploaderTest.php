<?php

namespace Tests\Upload\Unit;

use Mockery as m;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Vista\Upload\FileUploader;
use Vista\Upload\Info\Factory as InfoFactory;
use Vista\Upload\Info\Info;
use Vista\Upload\System\Factory as SystemFactory;
use Vista\Upload\System\System;

class FileUploaderTest extends TestCase
{
    private InfoFactory $infoFactory;
    private SystemFactory $systemFactory;

    public function setUp(): void
    {
        $this->infoFactory = m::mock(InfoFactory::class);
        $this->systemFactory = m::mock(SystemFactory::class);
    }

    public function tearDown(): void
    {
        m::close();
    }

    public function testConstruct(): void
    {
        $this->infoFactory->shouldReceive('create')
            ->once()
            ->with('file')
            ->andReturn(m::mock(Info::class));
        $this->systemFactory->shouldReceive('create')
            ->once()
            ->andReturn(m::mock(System::class));

        $uploader = new FileUploader('file', $this->systemFactory, $this->infoFactory);

        $this->assertInstanceOf(FileUploader::class, $uploader);
    }

    public function testConstructWithNoParameters(): void
    {
        $uploader = new FileUploader();

        $this->assertInstanceOf(FileUploader::class, $uploader);
        $this->assertEmpty($uploader->getOriginalFilename());
        $this->assertEmpty($uploader->getOriginalExtension());
        $this->assertEmpty($uploader->getResults());
        $this->assertEmpty($uploader->getErrors());
    }

    #[TestWith(['validate'])]
    #[TestWith(['upload'])]
    public function testWithFileInfoError(string $method): void
    {
        $info = m::mock(Info::class);
        $info->shouldReceive('getError')->once()
            ->andReturn(UPLOAD_ERR_NO_FILE);
        $this->infoFactory->shouldReceive('create')->once()->with('file')
            ->andReturn($info);

        $system = m::mock(System::class);
        $this->systemFactory->shouldReceive('create')->once()
            ->andReturn($system);

        $uploader = new FileUploader('file', $this->systemFactory, $this->infoFactory);

        $this->assertFalse($uploader->$method());
    }

    #[TestWith(['validate'])]
    #[TestWith(['upload'])]
    public function testWithBadMimeType(string $method): void
    {
        $info = m::mock(Info::class);
        $info->shouldReceive('getError')->once()
            ->andReturn(UPLOAD_ERR_OK);
        $info->shouldReceive('getMimeType')->once()
            ->andReturn('text/plain');
        $this->infoFactory->shouldReceive('create')->once()
            ->with('file')
            ->andReturn($info);

        $system = m::mock(System::class);
        $this->systemFactory->shouldReceive('create')->once()
            ->andReturn($system);

        $uploader = new FileUploader('file', $this->systemFactory, $this->infoFactory);
        $uploader->setAllowedMimeTypes(['image/jpeg', 'image/png']);

        $this->assertFalse($uploader->$method());
    }

    #[TestWith(['validate'])]
    #[TestWith(['upload'])]
    public function testWithBadFileSize(string $method): void
    {
        $info = m::mock(Info::class);
        $info->shouldReceive('getError')->once()
            ->andReturn(UPLOAD_ERR_OK);
        $info->shouldReceive('getMimeType')->once()
            ->andReturn('image/jpeg');
        $info->shouldReceive('getSize')->once()
            ->andReturn(2048);
        $this->infoFactory->shouldReceive('create')->once()
            ->with('file')
            ->andReturn($info);

        $system = m::mock(System::class);
        $this->systemFactory->shouldReceive('create')->once()
            ->andReturn($system);

        $uploader = new FileUploader('file', $this->systemFactory, $this->infoFactory);
        $uploader->setAllowedMimeTypes(['image/jpeg', 'image/png'])
            ->setMaxFileSize(1024);

        $this->assertFalse($uploader->$method());
    }

    #[TestWith(['validate'])]
    #[TestWith(['upload'])]
    public function testWithoutUploadDir(string $method): void
    {
        $info = m::mock(Info::class);
        $info->shouldReceive('getError')->once()->andReturn(UPLOAD_ERR_OK);
        $info->shouldReceive('getMimeType')->once()
            ->andReturn('image/jpeg');
        $info->shouldReceive('getSize')->once()
            ->andReturn(1024);
        $this->infoFactory->shouldReceive('create')->once()
            ->with('file')
            ->andReturn($info);

        $system = m::mock(System::class);
        $this->systemFactory->shouldReceive('create')->once()
            ->andReturn($system);

        $uploader = new FileUploader('file', $this->systemFactory, $this->infoFactory);
        $uploader->setAllowedMimeTypes(['image/jpeg', 'image/png'])
            ->setMaxFileSize(1024);

        $this->assertFalse($uploader->$method());
    }

    public function testValidate(): void
    {
        $info = m::mock(Info::class);
        $info->shouldReceive('getError')->once()
            ->andReturn(UPLOAD_ERR_OK);
        $info->shouldReceive('getMimeType')->once()
            ->andReturn('image/jpeg');
        $info->shouldReceive('getSize')->once()
            ->andReturn(1024);
        $this->infoFactory->shouldReceive('create')->once()
            ->with('file')
            ->andReturn($info);

        $system = m::mock(System::class);
        $this->systemFactory->shouldReceive('create')->once()
            ->andReturn($system);

        $uploader = new FileUploader('file', $this->systemFactory, $this->infoFactory);
        $uploader->setAllowedMimeTypes(['image/jpeg', 'image/png'])
            ->setMaxFileSize(1024)
            ->setUploadDir('/tmp');

        $this->assertTrue($uploader->validate());
    }

    public function testUploadFileExistsReturnsFalse(): void
    {
        $info = m::mock(Info::class);
        $info->shouldReceive('getError')->once()
            ->andReturn(UPLOAD_ERR_OK);
        $info->shouldReceive('getMimeType')->once()
            ->andReturn('image/jpeg');
        $info->shouldReceive('getSize')->once()
            ->andReturn(1024);
        $info->shouldReceive('getFilename')->once()
            ->andReturn('test.jpg');
        $info->shouldReceive('getTmpName')->once()
            ->andReturn('/tmp/php1234');
        $this->infoFactory->shouldReceive('create')->once()
            ->with('file')
            ->andReturn($info);

        $system = m::mock(System::class);
        $system->shouldReceive('exists')->once()
            ->andReturn(true);
        $this->systemFactory->shouldReceive('create')
            ->once()
            ->andReturn($system);

        $uploader = new FileUploader('file', $this->systemFactory, $this->infoFactory);
        $uploader->setAllowedMimeTypes(['image/jpeg', 'image/png'])
            ->setMaxFileSize(1024)
            ->setUploadDir('/tmp');

        $this->assertFalse($uploader->upload());
    }

    public function testUploadFileExistsAllowOverwriteReturnsTrue(): void
    {
        $name = 'test.jpg';
        $tmpPath = '/tmp/test.jpg';

        $info = m::mock(Info::class);
        $info->shouldReceive('getError')->once()
            ->andReturn(UPLOAD_ERR_OK);
        $info->shouldReceive('getMimeType')->once()
            ->andReturn('image/jpeg');
        $info->shouldReceive('getSize')->once()
            ->andReturn(1024);
        $info->shouldReceive('getFilename')->once()
            ->andReturn('test.jpg');
        $info->shouldReceive('getTmpName')->once()
            ->andReturn($tmpPath);
        $this->infoFactory->shouldReceive('create')->once()
            ->with('file')
            ->andReturn($info);

        $system = m::mock(System::class);
        $system->shouldReceive('exists')->once()
            ->andReturn(true);
        $system->shouldReceive('moveUploadedFile')
            ->with($tmpPath, '/tmp/' . $name)
            ->once()
            ->andReturn(true);
        $this->systemFactory->shouldReceive('create')
            ->once()
            ->andReturn($system);

        $uploader = new FileUploader('file', $this->systemFactory, $this->infoFactory);
        $uploader->setAllowedMimeTypes(['image/jpeg', 'image/png'])
            ->setMaxFileSize(1024)
            ->allowOverwrite()
            ->setUploadDir('/tmp');

        $this->assertTrue($uploader->upload());
    }

    public function testUploadFileNotExistsReturnsTrue(): void
    {
        $name = 'test.jpg';
        $tmpPath = '/tmp/test.jpg';

        $info = m::mock(Info::class);
        $info->shouldReceive('getError')->once()
            ->andReturn(UPLOAD_ERR_OK);
        $info->shouldReceive('getMimeType')->once()
            ->andReturn('image/jpeg');
        $info->shouldReceive('getSize')->once()
            ->andReturn(1024);
        $info->shouldReceive('getFilename')->once()
            ->andReturn('test.jpg');
        $info->shouldReceive('getTmpName')->once()
            ->andReturn($tmpPath);
        $this->infoFactory->shouldReceive('create')->once()
            ->with('file')
            ->andReturn($info);

        $system = m::mock(System::class);
        $system->shouldReceive('exists')->once()
            ->andReturn(false);
        $system->shouldReceive('moveUploadedFile')
            ->with($tmpPath, $tmpPath)
            ->once()
            ->andReturn(true);
        $this->systemFactory->shouldReceive('create')
            ->once()
            ->andReturn($system);

        $uploader = new FileUploader('file', $this->systemFactory, $this->infoFactory);
        $uploader->setAllowedMimeTypes(['image/jpeg', 'image/png'])
            ->setMaxFileSize(1024)
            ->setUploadDir('/tmp');

        $this->assertTrue($uploader->upload());
        $this->assertEquals([
            [
                'filename' => $name,
                'destination' => '/tmp/' . $name,
            ],
        ], $uploader->getResults());
    }

    public function testGetOriginalFilename(): void
    {
        $info = m::mock(Info::class);
        $info->shouldReceive('getFilename')->once()
            ->andReturn('test.jpg');
        $this->infoFactory->shouldReceive('create')->once()
            ->with('file')
            ->andReturn($info);

        $system = m::mock(System::class);
        $this->systemFactory->shouldReceive('create')->once()
            ->andReturn($system);

        $uploader = new FileUploader('file', $this->systemFactory, $this->infoFactory);

        $this->assertSame('test.jpg', $uploader->getOriginalFilename());
    }

    public function testGetOriginalExtension(): void
    {
        $info = m::mock(Info::class);
        $info->shouldReceive('getExtension')->once()
            ->andReturn('jpg');
        $this->infoFactory->shouldReceive('create')->once()
            ->with('file')
            ->andReturn($info);

        $system = m::mock(System::class);
        $this->systemFactory->shouldReceive('create')->once()
            ->andReturn($system);

        $uploader = new FileUploader('file', $this->systemFactory, $this->infoFactory);

        $this->assertSame('jpg', $uploader->getOriginalExtension());
    }

    public function testDeleteFile(): void
    {
        $info = m::mock(Info::class);
        $this->infoFactory->shouldReceive('create')->once()
            ->with('')
            ->andReturn($info);

        $system = m::mock(System::class);
        $system->shouldReceive('exists')->once()
            ->with('test.jpg')
            ->andReturn(true);
        $system->shouldReceive('delete')->once()
            ->with('test.jpg')
            ->andReturn(true);
        $this->systemFactory->shouldReceive('create')->once()
            ->andReturn($system);

        $uploader = new FileUploader('', $this->systemFactory, $this->infoFactory);
        $uploader->setUploadDir('/tmp');

        $this->assertTrue($uploader->delete('test.jpg'));
    }
}
