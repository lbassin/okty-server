<?php

declare(strict_types=1);

namespace App\Repository\Learning;

use App\Entity\Learning\Chapter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

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

    public function findById(string $id): Chapter
    {
        /** @var Chapter $chapter */
        $chapter = $this->repository->find($id);
        if (!$chapter) {
            throw new EntityNotFoundException();
        }

        return $chapter;
    }
}
