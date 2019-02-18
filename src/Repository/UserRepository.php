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
        if (empty($data['id']) || empty($data['login']) || empty($data['email'])) {
            throw new \LogicException('Some data are missing from the API');
        }

        $apiId = $data['id'];
        $username = $data['login'];
        $email = $data['email'];
        $name = $data['name'] ?? '';
        $avatar = $data['avatar_url'] ?? '';

        $user = new User($username, $email, $name, $avatar, self::GITHUB_PROVIDER, $apiId);

        return $user;
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

    public function findByUsername(string $username): ?User
    {
        /** @var User $user */
        $user = $this->repository->findOneBy(['username' => $username]);

        return $user;
    }
}
