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
        $responseCommunity = $this->httpClient->request('GET', "https://hub.docker.com/api/content/v1/products/search?source=community&q=$search&page_size=25", ['headers' => ['Search-Version' => 'v3']]);
        $responseOfficial = $this->httpClient->request('GET', "https://hub.docker.com/api/content/v1/products/search?image_filter=store%2Cofficial&q=$search&page_size=25", ['headers' => ['Search-Version' => 'v3']]);

        $summariesCommunity = (new Json($responseCommunity->getContent()))->getValue()['summaries'];
        $summariesOfficial = (new Json($responseOfficial->getContent()))->getValue()['summaries'];

        $response = array_merge($summariesOfficial ?? [], $summariesCommunity);

        return new JsonResponse(
            $this->serializer->serialize($response, 'json'),
            Response::HTTP_OK,
            [],
            true
        );
    }
}