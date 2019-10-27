<?php

declare(strict_types=1);

namespace App\Tests\Domain\Generator\ValueObject\DockerCompose;

use App\Domain\Generator\Exception\DockerCompose\UnknownDockerComposeVersionException;
use App\Domain\Generator\ValueObject\DockerCompose\Version;
use PHPUnit\Framework\TestCase;

class VersionTest extends TestCase
{
    /**
     * @dataProvider wrongVersionProvider
     */
    public function test_exception_thrown_if_wrong_version_value(string $version): void
    {
        $this->expectException(UnknownDockerComposeVersionException::class);

        new Version($version);
    }

    /**
     * @dataProvider availableVersionProvider
     */
    public function test_existing_version_are_allowed(string $value): void
    {
        $version = new Version($value);

        $this->assertInstanceOf(Version::class, $version);
    }

    public function wrongVersionProvider(): array
    {
        return [
            ['WrongVersion'],
            [''],
            [4],
            ['Won\'t work'],
            ['0.14'],
        ];
    }

    public function availableVersionProvider(): array
    {
        $reflectionClass = new \ReflectionClass(Version::class);

        return array_map(function ($version) {
            return [$version];
        }, $reflectionClass->getConstant('AVAILABLE_VERSIONS'));
    }
}
