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
        $authUrl = "https://auth.docker.io/token?scope=repository:$imageName:pull&service=registry.docker.io";

        $imageAccessToken = $this->httpClient->request('GET', $authUrl);
        $authResponse = (new Json($imageAccessToken->getContent()))->getValue();

        $apiUrl = "https://registry.hub.docker.com/v2/$imageName/tags/list";

        $response = $this->httpClient->request('GET', $apiUrl, [
            'headers' => [
                'Authorization' => "Bearer " . $authResponse['access_token']
            ]
        ]);

        return new JsonResponse(
            $this->serializer->serialize($response->getContent(), 'json'),
            Response::HTTP_OK,
            [],
            true
        );
    }
}