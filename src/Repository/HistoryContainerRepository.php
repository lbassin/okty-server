<?php

namespace App\Repository;

use App\Entity\HistoryContainer;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class HistoryContainerRepository implements HistoryContainerRepositoryInterface
{
    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(HistoryContainer::class);
    }
}
