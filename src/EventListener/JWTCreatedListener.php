<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Repository\UserRepositoryInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTCreatedListener
{
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $payload = $event->getData();
        if (empty($payload['username'])) {
            return;
        }

        $user = $this->userRepository->findById($payload['username']);

        $payload['login'] = $user->getLogin();

        $event->setData($payload);
    }
}
