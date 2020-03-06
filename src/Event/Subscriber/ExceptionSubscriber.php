<?php

declare(strict_types=1);

namespace App\Event\Subscriber;

use App\Exception\Handler\ExceptionHandler;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Traversable;

class ExceptionSubscriber implements EventSubscriberInterface
{
    private $handlers;
    private $logger;

    public function __construct(Traversable $handlers, LoggerInterface $logger)
    {
        $this->handlers = $handlers;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onException', 0],
        ];
    }

    public function onException(ExceptionEvent $event): void
    {
        /** @var ExceptionHandler $handler */
        foreach ($this->handlers as $handler) {
            if (!$handler->supports($event->getException())) {
                continue;
            }

            $handler->handle($event);

            return;
        }

        $this->logger->error($event->getException()->getMessage(), $event->getException()->getTrace());

        $event->setResponse(
            new JsonResponse([
                'message' => 'Oups, something went wrong.',
            ])
        );
    }
}
