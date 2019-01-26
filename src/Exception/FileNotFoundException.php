<?php

declare(strict_types=1);

namespace App\Exception;

use Throwable;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class FileNotFoundException extends \RuntimeException
{

    public function __construct(string $message = "", int $code = 404, Throwable $previous = null)
    {
        parent::__construct(sprintf("File %s not found", $message), $code, $previous);
    }
}