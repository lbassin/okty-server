<?php

declare(strict_types=1);

namespace App\Controller\Api\Registry;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @author Maxime Marquet <maxime.marquet1@gmail.com>
 */
class Search
{
    private $serializer;
    private $httpClient;

    public function __construct(SerializerInterface $serializer, HttpClientInterface $httpClient)
    {
        $this->serializer = $serializer;
        $this->httpClient = $httpClient;
    }

    /**
     * @Route("registry/search", methods={"GET"})
     */
    public function handle(Request $request): JsonResponse
    {
        $search = $request->query->get('query');
        $response = $this->httpClient->request('GET', "https://hub.docker.com/v2/search/repositories/?query=$search&page_size=25");

        return new JsonResponse(
            $this->serializer->serialize($response->getContent(), 'json'),
            Response::HTTP_OK,
            [],
            true
        );
    }
}