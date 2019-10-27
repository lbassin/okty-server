<?php

declare(strict_types=1);

namespace App\Domain\Generator\Exception\DockerCompose\Service\Id;

use RuntimeException;

class WrongServiceIdFormatException extends RuntimeException
{
    public function __construct(string $id)
    {
        $message = sprintf('Service ID provided do not match expected format (%s)', $id);

        parent::__construct($message, 0, null);
    }
}
