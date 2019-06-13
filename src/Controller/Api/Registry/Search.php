<?php

declare(strict_types=1);

namespace App\Controller\Api\Registry;

use App\Service\Hub;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Maxime Marquet <maxime.marquet1@gmail.com>
 */
class Search
{
    private $serializer;
    private $hub;

    public function __construct(SerializerInterface $serializer, Hub $hub)
    {
        $this->serializer = $serializer;
        $this->hub = $hub;
    }

    /**
     * @Route("registry/search", methods={"GET"})
     */
    public function handle(Request $request): JsonResponse
    {
        $search = $request->query->get('query');

        $images = $this->hub->searchImages($search);

        return new JsonResponse(
            $this->serializer->serialize($images, 'json'),
            Response::HTTP_OK,
            [],
            true
        );
    }

}
