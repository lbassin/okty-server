<?php declare(strict_types=1);

namespace App\Controller\Api\Container;

use App\Repository\ContainerRepositoryInterface;
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
    private $containerRepository;
    private $serializer;

    public function __construct(ContainerRepositoryInterface $containerRepository, SerializerInterface $serializer)
    {
        $this->containerRepository = $containerRepository;
        $this->serializer = $serializer;
    }

    /**
     * @Route("containers/{id}", methods={"GET"}, requirements={"id": "^[a-zA-Z]+(-)?[a-zA-Z]+$"})
     */
    public function handle(Request $request): Response
    {
        $container = $this->containerRepository->findOneById($request->attributes->get('id'));

        return new JsonResponse(
            $this->serializer->serialize($container, 'json'),
            Response::HTTP_OK,
            [],
            true
        );
    }
}
