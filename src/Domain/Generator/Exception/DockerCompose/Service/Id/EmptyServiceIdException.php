<?php

declare(strict_types=1);

namespace App\Domain\Generator\Exception\DockerCompose\Service\Id;

use RuntimeException;

class EmptyServiceIdException extends RuntimeException
{
    public function __construct()
    {
        $message = 'Empty value is not allowed for service ID';

        parent::__construct($message, 0, null);
    }
}
