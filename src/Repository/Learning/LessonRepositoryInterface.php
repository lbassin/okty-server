<?php

declare(strict_types=1);

namespace App\Repository\Learning;

use App\Entity\Learning\Chapter;
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

    public function createFromValueObject(\App\ValueObject\Learning\Github\Lesson $lessonValue, Chapter $chapter): Lesson;

    public function save(Lesson $lesson): void;
}
