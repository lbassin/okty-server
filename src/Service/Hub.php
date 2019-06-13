<?php

declare(strict_types=1);

namespace App\Service;

use App\ValueObject\Json;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Hub
{
    private $httpClient;
    private $hubUrl;
    private $pageSize;
    private $authUrl;

    public function __construct(HttpClientInterface $httpClient, string $hubUrl, int $pageSize, string $authUrl)
    {
        $this->httpClient = $httpClient;
        $this->hubUrl = $hubUrl;
        $this->pageSize = $pageSize;
        $this->authUrl = $authUrl;
    }

    public function searchImages(string $name): array
    {
        $searchUrl = $this->hubUrl.'/content/v1/products/search';

        $responseCommunity = $this->httpClient->request(
            'GET',
            "$searchUrl/?source=community&q=$name&page_size=$this->pageSize",
            ['headers' => ['Search-Version' => 'v3']]
        );
        $dataCommunity = new Json($responseCommunity->getContent());

        $responseOfficial = $this->httpClient->request('GET',
            "$searchUrl?image_filter=store%2Cofficial&q=$name&page_size=$this->pageSize",
            ['headers' => ['Search-Version' => 'v3']]
        );
        $dataOfficial = new Json($responseOfficial->getContent());

        $dataOfficial = $dataOfficial->getData('summaries') ?? [];
        $dataCommunity = $dataCommunity->getData('summaries') ?? [];

        $response = array_merge($dataOfficial, $dataCommunity);

        return $response;
    }

    public function getTags(string $image): array
    {
        $slashCount = substr_count($image, '/');
        if ($slashCount >= 2) {
            return [];
        }

        $image = $slashCount > 0 ? $image : "library/$image";
        $authUrl = $this->authUrl."?scope=repository:$image:pull&service=registry.docker.io";

        $imageAccessToken = $this->httpClient->request('GET', $authUrl);

        $authResponse = new Json($imageAccessToken->getContent());

        $apiUrl = "https://registry.hub.docker.com/v2/$image/tags/list";

        $response = $this->httpClient->request('GET', $apiUrl, [
            'headers' => [
                'Authorization' => "Bearer ".$authResponse->getData('access_token'),
            ],
        ]);

        $tags = new JSON($response->getContent());

        return $tags->getData('tags');
    }

}
