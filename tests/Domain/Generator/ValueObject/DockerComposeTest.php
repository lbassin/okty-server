<?php

declare(strict_types=1);

namespace Tests\Domain\Generator\ValueObject;

use App\Domain\Generator\Exception\DockerCompose\NoServiceProvidedException;
use App\Domain\Generator\Exception\DockerCompose\WrongServiceValueProvidedException;
use App\Domain\Generator\ValueObject\DockerCompose;
use App\Domain\Generator\ValueObject\DockerCompose\Service;
use PHPUnit\Framework\TestCase;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class DockerComposeTest extends TestCase
{
    /**
     * @dataProvider wrong_services_provider
     */
    public function test_services_array_with_somenthing_else_than_service(array $services): void
    {
        $this->expectException(WrongServiceValueProvidedException::class);

        /** @var DockerCompose\Version $version */
        $version = $this->createMock(DockerCompose\Version::class);

        new DockerCompose($version, $services);
    }

    public function test_no_service_provided(): void
    {
        $this->expectException(NoServiceProvidedException::class);

        /** @var DockerCompose\Version $version */
        $version = $this->createMock(DockerCompose\Version::class);

        new DockerCompose($version, []);

    }

    public function wrong_services_provider(): array
    {
        return [
            [[$this->createMock(Service::class), 'salut', $this->createMock(Service::class)]],
            [[$this->createMock(Service::class), null, $this->createMock(Service::class)]],
            [[$this->createMock(Service::class), 12, $this->createMock(Service::class)]],
            [['ok', $this->createMock(Service::class)]],
        ];
    }
}
