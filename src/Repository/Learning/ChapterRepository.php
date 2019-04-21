<?php

declare(strict_types=1);

namespace App\Repository\Learning;

use App\Entity\Chapter;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class ChapterRepository implements ChapterRepositoryInterface
{
    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Chapter::class);
    }

    public function findAll(): array
    {
        return $this->repository->findBy([], ['position' => 'ASC']);
    }

}
