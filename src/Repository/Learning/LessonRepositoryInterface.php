<?php

declare(strict_types=1);

namespace App\Repository\Learning;

use App\Entity\Learning\Lesson;
use Doctrine\ORM\EntityNotFoundException;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
interface LessonRepositoryInterface
{
    public function findAll(): array;

    public function findByChapterId(string $id): array;

    /** @throws EntityNotFoundException */
    public function findByChapterAndId(string $chapterId, string $lessonId): Lesson;
}
