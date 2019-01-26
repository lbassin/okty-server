<?php

declare(strict_types=1);

namespace App\Exception;

use Throwable;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class BadCredentialsException extends \RuntimeException
{
    public function __construct(string $service, int $code = 401, Throwable $previous = null)
    {
        $message = sprintf("Cannot connect to %s", $service);

        parent::__construct($message, $code, $previous);
    }
}
