<?php

declare(strict_types=1);

namespace App\Domain\Generator\Exception\DockerCompose;

use RuntimeException;

class WrongServiceValueProvidedException extends RuntimeException
{
    public function __construct(string $wrongType)
    {
        $message = sprintf('A service was expected but an instance of %s was provided', $wrongType);

        parent::__construct($message, 0, null);
    }
}