<?php

declare(strict_types=1);

namespace App\Controller\Api\User\History;

use App\Builder\ValueObject\ContainerArgs;
use App\Entity\History;
use App\Repository\HistoryContainerRepositoryInterface;
use App\Repository\HistoryRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\ValueObject\Json;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Create
{
    private $historyRepository;
    private $tokenManager;
    private $tokenStorage;
    private $userRepository;
    private $historyContainerRepository;

    public function __construct(
        HistoryRepositoryInterface $historyRepository,
        HistoryContainerRepositoryInterface $historyContainerRepository,
        UserRepositoryInterface $userRepository,
        JWTTokenManagerInterface $tokenManager,
        TokenStorageInterface $tokenStorage
    ) {
        $this->historyRepository = $historyRepository;
        $this->historyContainerRepository = $historyContainerRepository;
        $this->userRepository = $userRepository;
        $this->tokenManager = $tokenManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route("user/history", methods={"POST"})
     */
    public function handle(Request $request): JsonResponse
    {
        $token = $this->tokenManager->decode($this->tokenStorage->getToken());
        $user = $this->userRepository->findById($token['username']);

        $data = (new Json($request->getContent()))->getValue();

        $history = new History($user);
        foreach ($data['containers'] as $container) {
            $args = new ContainerArgs($container);
            $historyContainer = $this->historyContainerRepository->createFromArgs($history, $args);

            $history->addContainer($historyContainer);
        }
        $this->historyRepository->save($history);

        return new JsonResponse('', Response::HTTP_CREATED);
    }
}
