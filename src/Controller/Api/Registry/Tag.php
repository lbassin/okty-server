<?php

declare(strict_types=1);

namespace App\Controller\Api\Registry;

use App\ValueObject\Json;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @author Maxime Marquet <maxime.marquet1@gmail.com>
 */
class Tag
{
    private $serializer;
    private $httpClient;

    public function __construct(SerializerInterface $serializer, HttpClientInterface $httpClient)
    {
        $this->serializer = $serializer;
        $this->httpClient = $httpClient;
    }

    /**
     * @Route("registry/tag", methods={"GET"})
     */
    public function handle(Request $request): JsonResponse
    {
        $imageName = $request->query->get('query');

        $slashCount = substr_count($imageName, '/');
        if ($slashCount >= 2) {
            return new JsonResponse([]);
        }

        $imageName = $slashCount > 0 ? $imageName : "library/$imageName";
        $apiRoute = "https://hub.docker.com/v2/repositories/$imageName/tags/";

        do {
            $response = $this->httpClient->request('GET', $apiRoute);
            $tags = array_merge($tags ?? [], (new Json($response->getContent()))->getValue()['results']);
            $apiRoute = (new Json($response->getContent()))->getValue()['next'];
        } while($apiRoute);

        return new JsonResponse(
            $this->serializer->serialize($tags, 'json'),
            Response::HTTP_OK,
            [],
            true
        );
    }
}