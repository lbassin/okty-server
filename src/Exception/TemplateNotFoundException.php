<?php

declare(strict_types=1);

namespace App\Exception;

use Throwable;

class TemplateNotFoundException extends \RuntimeException
{
    public function __construct(string $template, Throwable $previous = null)
    {
        $message = sprintf('Template %s not found', $template);

        parent::__construct($message, 0, $previous);
    }
}
