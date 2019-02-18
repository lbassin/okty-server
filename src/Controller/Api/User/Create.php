<?php

declare(strict_types=1);

namespace App\Controller\Api\User;

use App\Provider\Github;
use App\Repository\UserRepositoryInterface;
use App\ValueObject\Json;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Create
{
    private $github;
    private $userRepository;
    private $tokenManager;

    public function __construct(
        Github $github,
        UserRepositoryInterface $userRepository,
        JWTTokenManagerInterface $tokenManager
    ) {
        $this->github = $github;
        $this->userRepository = $userRepository;
        $this->tokenManager = $tokenManager;
    }

    /**
     * @Route("users", methods={"POST"})
     */
    public function handle(Request $request): Response
    {
        $args = (new Json($request->getContent()))->getValue();

        $accessToken = $this->github->auth($args['code'] ?? '', $args['state'] ?? '');
        $apiData = $this->github->getUser($accessToken);

        $user = $this->userRepository->findByProvider($apiData['id'] ?? 0, UserRepositoryInterface::GITHUB_PROVIDER);
        if (!$user) {
            $user = $this->userRepository->createFromGithub($apiData);
        }

        $user->updateToken($accessToken);
        $this->userRepository->save($user);

        return new JsonResponse([
            'token' => $this->tokenManager->create($user)
        ]);
    }
}
