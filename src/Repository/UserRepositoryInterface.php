<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
interface UserRepositoryInterface
{
    public const GITHUB_PROVIDER = 'github';
    public const GITLAB_PROVIDER = 'gitlab';

    public function createFromGithub(array $user): User;

    public function createFromGitlab(array $user): User;

    public function save(User $user): void;

    public function findByProvider(int $getApiId, string $string): ?User;

    public function findById(string $id): ?User;
}
