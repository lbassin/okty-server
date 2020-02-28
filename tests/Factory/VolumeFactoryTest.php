<?php

declare(strict_types=1);

use App\Entity\Volume\DockerVolume;
use App\Entity\Volume\SharedVolume;
use App\Factory\VolumeFactory;
use PHPUnit\Framework\TestCase;

class VolumeFactoryTest extends TestCase
{
    public function test_build_with_empty_data(): void
    {
        $ports = (new VolumeFactory())->createAll([]);

        $this->assertEmpty($ports);
    }

    public function test_build_with_right_data(): void
    {
        $volumes = (new VolumeFactory())->createAll([
            ['type' => 'shared', 'host' => './src', 'container' => '/app'],
            ['type' => 'docker', 'name' => 'mysql-data', 'container' => '/var/db/data'],
        ]);

        $this->assertCount(2, $volumes);
        $this->assertInstanceOf(SharedVolume::class, $volumes[0]);
        $this->assertInstanceOf(DockerVolume::class, $volumes[1]);
    }

    public function test_build_unknown_type(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new VolumeFactory())->createAll([
            ['type' => 'test'],
        ]);
    }
}
