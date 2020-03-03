<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;

interface ExceptionHandler
{
    public function supports(\Throwable $throwable): bool;

    public function handle(ExceptionEvent $exceptionEvent): void;
}
