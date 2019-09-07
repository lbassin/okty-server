<?php

declare(strict_types=1);

use App\ValueObject\DockerCompose;
use App\ValueObject\Service;
use PHPUnit\Framework\TestCase;

class DockerComposeTest extends TestCase
{

    public function testArrayNotService()
    {
        $services = [
            $service = $this->createMock(Service::class),
            'test',
        ];

        $this->expectException(InvalidArgumentException::class);

        new DockerCompose($services);
    }
}
