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
        $request = $this->getRequestWithVolumes([]);

        $ports = (new VolumeFactory())->createAll($request);

        $this->assertEmpty($ports);
    }

    public function test_build_with_right_data(): void
    {
        $request = $this->getRequestWithVolumes([
            ['type' => 'shared', 'host' => './src', 'container' => '/app'],
            ['type' => 'docker', 'name' => 'mysql-data', 'container' => '/var/db/data'],
        ]);

        $volumes = (new VolumeFactory())->createAll($request);

        $this->assertCount(2, $volumes);
        $this->assertInstanceOf(SharedVolume::class, $volumes[0]);
        $this->assertInstanceOf(DockerVolume::class, $volumes[1]);
    }

    private function getRequestWithVolumes(array $ports): array
    {
        return [
            "template" => "test",
            "args" => [
                "volumes" => $ports,
            ],
        ];
    }
}
