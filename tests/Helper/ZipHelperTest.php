<?php

namespace App\Tests\Helper;

use App\Helper\ZipHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class ZipHelperTest extends TestCase
{
    /** @var ZipHelper */
    private $helper;
    /** @var MockObject|\ZipArchive */
    private $mockZipArchive;

    protected function setUp()
    {
        $this->mockZipArchive = $this->createMock(\ZipArchive::class);
        $this->helper = new ZipHelper($this->mockZipArchive);
    }

    public function testArchiveValid()
    {
        $this->helper = new ZipHelper(new \ZipArchive());

        $files = [['name' => 'file.txt', 'content' => 'Hello'], ['name' => 'data/config.json', 'content' => '{a: 2}']];

        $path = $this->helper->zip($files);
        $this->assertNotEmpty($path);

        $this->assertTrue(file_exists($path));

        $zip = new \ZipArchive();
        $zip->open($path);

        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals(2, $zip->count());

        $zip->close();
    }

    public function testArchiveWrongArgs()
    {
        $this->helper = new ZipHelper(new \ZipArchive());

        $files = [
            ['name' => 'file.txt', 'contents' => 'H'],
            ['name' => 'data/config.json', 'content' => ''],
            ['name' => 'index.html', 'content' => 'Welcome']
        ];

        $path = $this->helper->zip($files);
        $this->assertNotEmpty($path);

        $this->assertTrue(file_exists($path));

        $zip = new \ZipArchive();
        $zip->open($path);

        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals(1, $zip->count());

        $zip->close();
    }

    public function testCloseFailed()
    {
        $exception = new \Exception('error');
        $this->mockZipArchive->method('close')->willThrowException($exception);

        $files = [['name' => 'file.txt', 'content' => 'Hello'], ['name' => 'data/config.json', 'content' => '{a: 2}']];

        $this->expectException(\RuntimeException::class);

        $this->helper->zip($files);
    }

}
