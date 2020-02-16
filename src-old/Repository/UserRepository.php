<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class UserRepository implements UserRepositoryInterface
{
    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(User::class);
    }

    public function createFromGithub(array $data): User
    {
        if (empty($data['id'])) {
            throw new \LogicException('User ID is missing from Github API');
        }

        if (empty($data['login'])) {
            throw new \LogicException('Username is missing from Github API');
        }

        $apiId = $data['id'];
        $login = $data['login'];
        $email = $data['email'] ?? null;
        $name = $data['name'] ?? null;
        $avatar = $data['avatar_url'] ?? null;

        return new User($apiId, $login, $email, $name, $avatar, self::GITHUB_PROVIDER);
    }

    public function createFromGitlab(array $data): User
    {
        if (empty($data['id'])) {
            throw new \LogicException('User ID is missing from Gitlab API');
        }

        if (empty($data['username'])) {
            throw new \LogicException('Username is missing from Gitlab API');
        }

        $apiId = $data['id'];
        $login = $data['username'];
        $email = $data['email'] ?? null;
        $name = $data['name'] ?? null;
        $avatar = $data['avatar_url'] ?? null;

        return new User($apiId, $login, $email, $name, $avatar, self::GITLAB_PROVIDER);
    }

    public function save(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function findByProvider(int $apiId, string $provider): ?User
    {
        /** @var User $user */
        $user = $this->repository->findOneBy(['apiId' => $apiId, 'provider' => $provider]);

        return $user;
    }

    public function findById(string $id): ?User
    {
        /** @var User $user */
        $user = $this->repository->findOneBy(['id' => $id]);

        return $user;
    }
}
