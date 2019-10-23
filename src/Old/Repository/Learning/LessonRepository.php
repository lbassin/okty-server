<?php

declare(strict_types=1);

namespace App\Repository\Learning;

use App\Entity\Learning\Action;
use App\Entity\Learning\Chapter;
use App\Entity\Learning\Lesson;
use App\Entity\Learning\Step;
use App\ValueObject\Learning\Github\Lesson as GithubLesson;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Ramsey\Uuid\Uuid;

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

    public function createFromValueObject(GithubLesson $lessonValue, Chapter $chapter): Lesson
    {
        $steps = [];
        $position = 1;
        $language = $chapter->getLanguage();

        $lesson = new Lesson(
            $lessonValue->getIdByLanguage($language),
            $lessonValue->getNameByLanguage($language),
            $lessonValue->getPosition(),
            $chapter
        );

        /** @var \App\ValueObject\Learning\Github\Step $stepValue */
        foreach ($lessonValue->getSteps() as $stepValue) {
            $action = null;
            if ($stepValue->getAction()) {
                $action = new Action(
                    Uuid::uuid4()->toString(),
                    $stepValue->getAction()->getType(),
                    $stepValue->getAction()->getConfig($language),
                    $language
                );

                $this->entityManager->persist($action);
            }

            $step = new Step(
                Uuid::uuid4()->toString(),
                $position++,
                $stepValue->getTextByLanguage($language),
                $lesson,
                $action
            );

            $this->entityManager->persist($step);
            $steps[] = $step;
        }

        return $lesson;
    }

    public function save(Lesson $lesson): void
    {
        $this->entityManager->persist($lesson);
        $this->entityManager->flush();
    }
}
