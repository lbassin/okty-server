<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use App\Exception\ValidationException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ValidationExceptionHandler implements ExceptionHandler
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function supports(\Throwable $throwable): bool
    {
        return $throwable instanceof ValidationException;
    }

    public function handle(ExceptionEvent $exceptionEvent): void
    {
        $context = [];

        $this->logger->warning('Validation error', $context);

        $exceptionEvent->setResponse(new JsonResponse(
            ['message' => $exceptionEvent->getException()->getMessage()],
            Response::HTTP_BAD_REQUEST
        ));
    }
}
