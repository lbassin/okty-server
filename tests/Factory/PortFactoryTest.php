<?php

declare(strict_types=1);

use App\Factory\PortFactory;
use PHPUnit\Framework\TestCase;

class PortFactoryTest extends TestCase
{
    public function test_build_with_empty_data(): void
    {
        $ports = (new PortFactory())->createAll([]);

        $this->assertEmpty($ports);
    }

    public function test_build_with_right_data(): void
    {
        $ports = (new PortFactory())->createAll([
            ['container' => 3306, 'host' => 80],
            ['container' => 443, 'host' => '443'],
            ['container' => '8080', 'host' => '8080', 'local_only' => true],
            ['container' => '21', 'host' => 22, 'local_only' => false],
        ]);

        $this->assertCount(4, $ports);
        $this->assertSame('127.0.0.1:80:3306', (string) $ports[0]);
        $this->assertSame('127.0.0.1:443:443', (string) $ports[1]);
        $this->assertSame('127.0.0.1:8080:8080', (string) $ports[2]);
        $this->assertSame('22:21', (string) $ports[3]);
    }
}
