<?php

declare(strict_types=1);

namespace App\Exception\ValueObject;

use App\Exception\ValidationException;
use Throwable;

class EmptyVolumePathInContainerException extends ValidationException
{
    public function __construct(string $path, Throwable $previous = null)
    {
        $message = sprintf("%s is not a valid path inside the container", $path);

        parent::__construct($message, 0, $previous);
    }
}
