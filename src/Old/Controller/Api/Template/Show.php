<?php declare(strict_types=1);

namespace App\Controller\Api\Template;

use App\Repository\TemplateRepositoryInterface;
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
    private $templateRepository;
    private $serializer;

    public function __construct(TemplateRepositoryInterface $templateRepository, SerializerInterface $serializer)
    {
        $this->templateRepository = $templateRepository;
        $this->serializer = $serializer;
    }

    /**
     * @Route("templates/{id}", methods={"GET"}, requirements={"id": "^[a-zA-Z0-9]+$"})
     */
    public function handle(Request $request): Response
    {
        $templates = $this->templateRepository->findOneById($request->attributes->get('id'));

        return new JsonResponse(
            $this->serializer->serialize($templates, 'json'),
            Response::HTTP_OK,
            [],
            true
        );
    }
}
