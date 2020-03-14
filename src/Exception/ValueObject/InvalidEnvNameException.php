<?php

declare(strict_types=1);

namespace App\Exception\ValueObject;

use App\Exception\ValidationException;
use Throwable;

class InvalidEnvNameException extends ValidationException
{
    public function __construct(string $id, Throwable $previous = null)
    {
        $message = sprintf("%s is not a valid env name", $id);

        parent::__construct($message, 0, $previous);
    }
}
