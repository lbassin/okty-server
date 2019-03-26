<?php declare(strict_types=1);

namespace App\Controller\Api\Container;

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
    private $containerRepository;
    private $serializer;

    public function __construct(ContainerRepositoryInterface $containerRepository, SerializerInterface $serializer)
    {
        $this->containerRepository = $containerRepository;
        $this->serializer = $serializer;
    }

    /**
     * @Route("containers", methods={"GET"})
     */
    public function handle(): Response
    {
        $containers = $this->containerRepository->findAll();

        return new JsonResponse(
            $this->serializer->serialize($containers, 'json'),
            Response::HTTP_OK,
            [],
            true
        );
    }
}
