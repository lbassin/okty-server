<?php

declare(strict_types=1);

namespace App\Domain\Generator\Exception\DockerCompose;

use RuntimeException;

class NoServiceProvidedException extends RuntimeException
{
    public function __construct()
    {
        $message = 'At least one service is expected';

        parent::__construct($message, 0, null);
    }
}