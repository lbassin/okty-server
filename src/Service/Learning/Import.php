<?php

declare(strict_types=1);

namespace App\Service\Learning;

use App\Repository\Learning\ChapterRepositoryInterface;
use App\Repository\Learning\LessonRepositoryInterface;
use App\Service\Github;
use App\ValueObject\Learning\Github\Chapter;
use App\ValueObject\Learning\Github\Lesson;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Import
{
    private $github;
    private $serializer;
    private $chapterRepository;
    private $lessonRepository;

    public function __construct(
        Github $github,
        SerializerInterface $serializer,
        ChapterRepositoryInterface $chapterRepository,
        LessonRepositoryInterface $lessonRepository
    ) {
        $this->github = $github;
        $this->serializer = $serializer;
        $this->chapterRepository = $chapterRepository;
        $this->lessonRepository = $lessonRepository;
    }

    public function import(): void
    {
        $lessons = [];
        $chapters = $this->getChapters();

        /** @var Chapter $chapter */
        foreach ($chapters as $chapter) {
            $lessons[$chapter->getFilename()] = $this->getLessons($chapter);
        }

        $this->save($chapters, $lessons);
    }

    public function save(array $chapters, array $lessons): void
    {
        $this->chapterRepository->clear();

        /** @var Chapter $chapter */
        foreach ($chapters as $chapterValue) {

            foreach ($chapterValue->getAvailableLanguages() as $language) {
                $chapter = $this->chapterRepository->createFromValueObject($chapterValue, $language);
                $this->chapterRepository->save($chapter);

                foreach ($lessons[$chapterValue->getFilename()] as $lessonValue) {
                    $lesson = $this->lessonRepository->createFromValueObject($lessonValue, $chapter);
                    $this->lessonRepository->save($lesson);
                }
            }

        }
    }

    public function getChapters(): array
    {
        $position = 1;
        $chapters = [];
        $data = Yaml::parse($this->github->getFile('learning/chapters.yml'));

        foreach ($data['chapters'] ?? [] as $filename => $config) {
            $chapters[] = new Chapter($filename, $config, $position);

            $position++;
        }

        return $chapters;
    }

    public function getLessons(Chapter $chapter): array
    {
        $lessons = [];
        $position = 1;

        foreach ($chapter->getLessonsReferences() as $reference) {
            $path = sprintf("%s/%s/%s.yml", 'learning', $chapter->getFilename(), $reference);
            $data = Yaml::parse($this->github->getFile($path));

            foreach ($data['steps'] as $index => $step) {
                $data['steps'][$index]['text'] = $this->getStepText($chapter, $reference, $step['text']);
            }

            $lessons[] = new Lesson($data, $position);

            $position++;
        }

        return $lessons;
    }

    public function getStepText(Chapter $chapter, string $lesson, array $text): array
    {
        $output = [];

        foreach ($text as $language => $filename) {
            $path = sprintf("learning/%s/%s/%s.md", $chapter->getFilename(), $lesson, $filename);

            $output[$language] = $this->github->getFile($path);
        }

        return $output;
    }
}
