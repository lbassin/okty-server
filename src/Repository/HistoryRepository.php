<?php

namespace App\Repository;

use App\Entity\History;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class HistoryRepository implements HistoryRepositoryInterface
{
    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(History::class);
    }

    public function findAllByUserId(string $userId): array
    {
        return $this->repository->findBy(['user' => $userId]);
    }
}
