<?php

declare(strict_types=1);

namespace App\Exception;

use Throwable;

class HostPortAlreadyMappedException extends \RuntimeException
{
    public function __construct(string $port, Throwable $previous = null)
    {
        $message = sprintf('Port %s can only be mapped once', $port);

        parent::__construct($message, 0, $previous);
    }
}
