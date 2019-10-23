<?php

declare(strict_types=1);

namespace App\Domain\Generator\ValueObject\DockerCompose;

use App\Domain\Generator\Exception\DockerCompose\UnknownDockerComposeVersionException;

class Version
{
    private const AVAILABLE_VERSIONS = [
        '1.0',
        '2.0',
        '2.1',
        '2.2',
        '2.3',
        '2.4',
        '3.0',
        '3.1',
        '3.2',
        '3.3',
        '3.4',
        '3.5',
        '3.6',
        '3.7',
    ];

    public function __construct(string $version)
    {
        if (!\in_array($version, self::AVAILABLE_VERSIONS)) {
            throw new UnknownDockerComposeVersionException($version);
        }
    }
}
