<?php

declare(strict_types=1);

namespace App\Controller\Api\User;

use App\Repository\UserRepositoryInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Index
{
    private $tokenStorage;
    private $tokenManager;
    private $userRepository;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        JWTTokenManagerInterface $tokenManager,
        UserRepositoryInterface $userRepository
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->tokenManager = $tokenManager;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("user")
     */
    public function handle(): JsonResponse
    {
        $token = $this->tokenManager->decode($this->tokenStorage->getToken());
        $user = $this->userRepository->findByUsername($token['username']);

        return new JsonResponse([
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'avatar' => $user->getAvatar(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'provider' => $user->getProvider(),
        ]);
    }
}
