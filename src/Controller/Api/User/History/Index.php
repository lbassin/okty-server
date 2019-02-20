<?php

declare(strict_types=1);

namespace App\Controller\Api\User\History;

use App\Repository\HistoryRepositoryInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Index
{
    private $historyRepository;
    private $tokenManager;
    private $tokenStorage;

    public function __construct(
        HistoryRepositoryInterface $historyRepository,
        JWTTokenManagerInterface $tokenManager,
        TokenStorageInterface $tokenStorage
    ) {
        $this->historyRepository = $historyRepository;
        $this->tokenManager = $tokenManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route("user/history", methods={"GET"})
     */
    public function handle(): JsonResponse
    {
        $token = $this->tokenManager->decode($this->tokenStorage->getToken());
        $userId = $token['username'];

        $history = $this->historyRepository->findAllByUserId($userId);

        return new JsonResponse($history);
    }
}
