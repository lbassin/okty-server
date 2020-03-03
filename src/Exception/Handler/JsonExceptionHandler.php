<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use JsonException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class JsonExceptionHandler implements ExceptionHandler
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function supports(\Throwable $throwable): bool
    {
        return $throwable instanceof JsonException;
    }

    public function handle(ExceptionEvent $exceptionEvent): void
    {
        $context = [];

        $this->logger->warning('Invalid JSON', $context);

        $exceptionEvent->setResponse(new JsonResponse(
            ['message' => 'Invalid JSON in the payload'],
            Response::HTTP_BAD_REQUEST
        ));
    }
}
