<?php

declare(strict_types=1);

namespace App\Controller\Api\User;

use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use App\Service\Github as GithubService;
use App\Service\Gitlab as GitlabService;
use App\ValueObject\Json;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Login
{
    private $githubService;
    private $gitlabService;
    private $userRepository;
    private $tokenManager;

    public function __construct(
        GithubService $githubService,
        GitlabService $gitlabService,
        UserRepositoryInterface $userRepository,
        JWTTokenManagerInterface $tokenManager
    ) {
        $this->githubService = $githubService;
        $this->gitlabService = $gitlabService;
        $this->userRepository = $userRepository;
        $this->tokenManager = $tokenManager;
    }

    /**
     * @Route("/login", methods={"POST"})
     */
    public function handle(Request $request): Response
    {
        $args = (new Json($request->getContent()))->getValue();

        if (!method_exists($this, $args['provider'])) {
            throw new NotFoundHttpException();
        }

        $user = $this->{$args['provider']}($args);

        return new JsonResponse([
            'token' => $this->tokenManager->create($user),
        ]);
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private function github(array $args): User
    {
        $accessToken = $this->githubService->auth($args['code'] ?? '', $args['state'] ?? '');
        $apiData = $this->githubService->getUser($accessToken);

        $user = $this->userRepository->findByProvider($apiData['id'] ?? 0, UserRepositoryInterface::GITHUB_PROVIDER);
        if (!$user) {
            $user = $this->userRepository->createFromGithub($apiData);
        }

        $user->updateToken($accessToken);
        $this->userRepository->save($user);

        return $user;
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private function gitlab(array $args): User
    {
        $accessToken = $this->gitlabService->auth($args['code'] ?? '', $args['state'] ?? '');
        $apiData = $this->gitlabService->getUser($accessToken);

        $user = $this->userRepository->findByProvider($apiData['id'] ?? 0, UserRepositoryInterface::GITLAB_PROVIDER);
        if (!$user) {
            $user = $this->userRepository->createFromGitlab($apiData);
        }

        $user->updateToken($accessToken);
        $this->userRepository->save($user);

        return $user;
    }
}
