<?php

declare(strict_types=1);

namespace App\Exception\Learning;

use Throwable;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class ActionValidatorNotFound extends \RuntimeException
{
    public function __construct(string $type = "", int $code = 400, Throwable $previous = null)
    {
        $message = sprintf("Cannot found validator for %s", $type);

        parent::__construct($message, $code, $previous);
    }
}