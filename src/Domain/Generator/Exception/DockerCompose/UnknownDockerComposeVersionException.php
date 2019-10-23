<?php

declare(strict_types=1);

namespace App\Domain\Generator\Exception\DockerCompose;

use RuntimeException;

class UnknownDockerComposeVersionException extends RuntimeException
{
    public function __construct(string $version)
    {
        $message = sprintf('Unknown docker-compose version (%s)', $version);

        parent::__construct($message, 0, null);
    }
}
