<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use App\Exception\HostPortAlreadyMappedException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class HostPortAlreadyMappedHandler implements ExceptionHandler
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function supports(\Throwable $throwable): bool
    {
        return $throwable instanceof HostPortAlreadyMappedException;
    }

    public function handle(ExceptionEvent $exceptionEvent): void
    {
        $exceptionEvent->setResponse(new JsonResponse(
            ['message' => $exceptionEvent->getException()->getMessage()],
            Response::HTTP_BAD_REQUEST
        ));
    }
}
