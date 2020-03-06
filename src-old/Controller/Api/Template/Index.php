<?php declare(strict_types=1);

namespace App\Controller\Api\Template;

use App\Repository\ContainerRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Index
{
    private $templateRepository;
    private $serializer;

    public function __construct(ContainerRepositoryInterface $templateRepository, SerializerInterface $serializer)
    {
        $this->templateRepository = $templateRepository;
        $this->serializer = $serializer;
    }

    /**
     * @Route("templates", methods={"GET"})
     */
    public function handle(): Response
    {
        $templates = $this->templateRepository->findAll();

        return new JsonResponse(
            $this->serializer->serialize($templates, 'json', ['groups' => ['list']]),
            Response::HTTP_OK,
            [],
            true
        );
    }
}
