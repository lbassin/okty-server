<?php

namespace App\Tests\Provider;

use App\Provider\Cloud;
use DomainException;
use Gaufrette\Adapter;
use Gaufrette\Exception\FileAlreadyExists;
use Gaufrette\Filesystem;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class CloudTest extends TestCase
{
    /** @var Cloud */
    private $cloud;

    /** @var MockObject|Filesystem */
    private $mockFilesystem;
    /** @var MockObject|Adapter */
    private $mockAdapter;
    /** @var string */
    private $urlPrefix;

    protected function setUp()
    {
        $this->mockAdapter = $this->createMock(Adapter\GoogleCloudStorage::class);
        $this->mockFilesystem = $this->createMock(Filesystem::class);
        $this->mockFilesystem
            ->method('getAdapter')
            ->willReturn($this->mockAdapter);

        $this->mockAdapter->method('getBucket')->willReturn('bucket');
        $this->mockAdapter->method('getOptions')->willReturn(['directory' => 'dc']);

        $this->urlPrefix = 'http://download.com';

        $this->cloud = new Cloud($this->mockFilesystem, $this->urlPrefix);
    }

    public function testUploadValid()
    {
        $fileContent = file_get_contents(__DIR__ . '/Fixtures/file-upload.txt');

        $this->mockFilesystem
            ->expects($this->once())
            ->method('write')
            ->with($this->stringContains('okty'), $fileContent, true);

        $url = $this->cloud->upload(__DIR__ . '/Fixtures/file-upload.txt');

        $this->assertStringStartsWith('http://download.com/bucket/dc/', $url);
    }

    public function testUploadDirectoryOutputEmpty()
    {
        $this->mockAdapter->method('getOptions')->willReturn([]);

        $fileContent = file_get_contents(__DIR__ . '/Fixtures/file-upload.txt');

        $this->mockFilesystem
            ->expects($this->once())
            ->method('write')
            ->with($this->stringContains('okty'), $fileContent, true);

        $url = $this->cloud->upload(__DIR__ . '/Fixtures/file-upload.txt');

        $this->assertStringStartsWith('http://download.com/bucket/', $url);
    }

    public function testWrongCredentials()
    {
        $this->mockFilesystem
            ->method('write')
            ->willThrowException(new DomainException());

        $this->expectException(AccessDeniedException::class);

        $this->cloud->upload('test');
    }

    public function testFileAlreadyExists()
    {
        $this->mockFilesystem
            ->method('write')
            ->willThrowException(new FileAlreadyExists('test'));

        $this->expectException(AccessDeniedException::class);

        $this->cloud->upload('test');
    }

    public function testInvalidPath()
    {
        $this->mockFilesystem
            ->method('write')
            ->willThrowException(new InvalidArgumentException());

        $this->expectException(AccessDeniedException::class);

        $this->cloud->upload('test');
    }

    public function testUploadFail()
    {
        $this->mockFilesystem
            ->method('write')
            ->willThrowException(new \RuntimeException());

        $this->expectException(AccessDeniedException::class);

        $this->cloud->upload('test');
    }
}
