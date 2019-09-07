<?php

declare(strict_types=1);

use App\ValueObject\File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    public function testEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new File('', 'content');
    }

    public function testEmptyContent(): void
    {
        $file = new File('test', '');

        $this->assertEmpty($file->getContent());
    }

    public function testRightContent(): void
    {
        $content = 'This is the content of the file';

        $file = new File('test', $content);

        $this->assertEquals($content, $file->getContent());
    }

    public function testRightName(): void
    {
        $title = 'title.txt';
        $file = new File($title, 'content');

        $this->assertEquals($title, $file->getName());
    }
}
