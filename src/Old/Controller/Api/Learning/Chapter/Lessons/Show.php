<?php

declare(strict_types=1);

namespace App\Controller\Api\Learning\Chapter\Lessons;

use App\Repository\Learning\LessonRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class Show
{
    private $serializer;
    private $lessonRepository;

    public function __construct(SerializerInterface $serializer, LessonRepositoryInterface $lessonRepository)
    {
        $this->serializer = $serializer;
        $this->lessonRepository = $lessonRepository;
    }

    /**
     * @Route("/learning/chapters/{chapter}/lessons/{lesson}", methods={"GET"})
     */
    public function handle(Request $request): Response
    {
        $chapterId = $request->attributes->get('chapter');
        $lessonId = $request->attributes->get('lesson');

        $lesson = $this->lessonRepository->findByChapterAndId($chapterId, $lessonId);

        return new JsonResponse(
            $this->serializer->serialize($lesson, 'json', ['groups' => ['lesson_show']]),
            Response::HTTP_OK,
            [],
            true
        );
    }
}