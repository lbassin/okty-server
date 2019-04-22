<?php

declare(strict_types=1);

namespace App\Repository\Learning;

use App\Entity\Learning\Lesson;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class LessonRepository implements LessonRepositoryInterface
{
    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Lesson::class);
    }

    public function findAll(): array
    {
        return $this->repository->findBy([], ['position' => 'ASC']);
    }

    public function findByChapterId(string $id): array
    {
        return $this->repository->findBy(['chapter' => $id], ['position' => 'ASC']);
    }

    public function findByChapterAndId(string $chapterId, string $lessonId): Lesson
    {
        /** @var Lesson $lesson */
        $lesson = $this->repository->findOneBy([
            'id' => $lessonId,
            'chapter' => $chapterId,
        ]);

        if (!$lesson) {
            throw new EntityNotFoundException();
        }

        return $lesson;
    }
}
