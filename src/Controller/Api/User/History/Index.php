<?php

declare(strict_types=1);

namespace App\Controller\Api\User\History;

use App\Repository\HistoryRepositoryInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Index
{
    private $historyRepository;
    private $tokenManager;
    private $tokenStorage;
    private $serializer;

    public function __construct(
        HistoryRepositoryInterface $historyRepository,
        JWTTokenManagerInterface $tokenManager,
        TokenStorageInterface $tokenStorage,
        SerializerInterface $serializer
    ) {
        $this->historyRepository = $historyRepository;
        $this->tokenManager = $tokenManager;
        $this->tokenStorage = $tokenStorage;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/user/history", methods={"GET"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function handle(): JsonResponse
    {
        $token = $this->tokenManager->decode($this->tokenStorage->getToken());
        $userId = $token['username'];

        $history = $this->historyRepository->findAllByUserId($userId);

        return new JsonResponse(
            $this->serializer->serialize($history, 'json'),
            Response::HTTP_OK,
            [],
            true
        );
    }
}
