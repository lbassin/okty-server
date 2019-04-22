<?php declare(strict_types=1);

namespace App\Controller\Api\Learning\Chapter;

use App\Repository\Learning\ChapterRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Show
{
    private $serializer;
    private $chapterRepository;

    public function __construct(SerializerInterface $serializer, ChapterRepositoryInterface $chapterRepository)
    {
        $this->serializer = $serializer;
        $this->chapterRepository = $chapterRepository;
    }

    /**
     * @Route("/learning/chapters/{id}", methods={"GET"})
     */
    public function handle(Request $request): Response
    {
        $id = $request->attributes->get('id');
        $chapter = $this->chapterRepository->findById($id);

        return new JsonResponse(
            $this->serializer->serialize($chapter, 'json', ['groups' => ['show']]),
            Response::HTTP_OK,
            [],
            true
        );
    }
}
