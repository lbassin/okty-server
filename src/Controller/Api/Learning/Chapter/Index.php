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
class Index
{
    private $serializer;
    private $chapterRepository;

    public function __construct(SerializerInterface $serializer, ChapterRepositoryInterface $chapterRepository)
    {
        $this->serializer = $serializer;
        $this->chapterRepository = $chapterRepository;
    }

    /**
     * @Route("/learning/chapters", methods={"GET"})
     */
    public function handle(Request $request): Response
    {
        $language = $request->query->get('lang', 'en_US');
        $chapters = $this->chapterRepository->findAll($language);

        return new JsonResponse(
            $this->serializer->serialize($chapters, 'json', ['groups' => ['list']]),
            Response::HTTP_OK,
            [],
            true
        );
    }
}
