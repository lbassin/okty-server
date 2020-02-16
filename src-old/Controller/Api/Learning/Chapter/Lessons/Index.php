<?php

declare(strict_types=1);

namespace App\Controller\Api\Learning\Chapter\Lessons;

use App\Repository\Learning\LessonRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class Index
{
    private $serializer;
    private $lessonRepository;

    public function __construct(SerializerInterface $serializer, LessonRepositoryInterface $lessonRepository)
    {
        $this->serializer = $serializer;
        $this->lessonRepository = $lessonRepository;
    }

    /**
     * @Route("/learning/chapters/{id}/lessons", methods={"GET"})
     */
    public function handle(Request $request): Response
    {
        $id = $request->attributes->get('id');
        $lessons = $this->lessonRepository->findByChapterId($id);

        return new JsonResponse(
            $this->serializer->serialize($lessons, 'json', ['groups' => ['lesson_list']]),
            Response::HTTP_OK,
            [],
            true
        );
    }
}