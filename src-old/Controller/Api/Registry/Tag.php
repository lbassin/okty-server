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
class Tag
{
    private $serializer;
    private $hub;

    public function __construct(SerializerInterface $serializer, Hub $hub)
    {
        $this->serializer = $serializer;
        $this->hub = $hub;
    }

    /**
     * @Route("registry/tag", methods={"GET"})
     */
    public function handle(Request $request): JsonResponse
    {
        $imageName = $request->query->get('query');
        $tags = $this->hub->getTags($imageName);

        return new JsonResponse(
            $this->serializer->serialize($tags, 'json'),
            Response::HTTP_OK,
            [],
            true
        );
    }
}