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

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function searchImages(string $name): array
    {
        $searchUrl = 'https://hub.docker.com/api/content/v1/products/search';

        $responseCommunity = $this->httpClient->request(
            'GET',
            "$searchUrl?source=community&q=$name&page_size=25",
            ['headers' => ['Search-Version' => 'v3']]
        );
        $dataCommunity = new Json($responseCommunity->getContent());

        $responseOfficial = $this->httpClient->request('GET',
            "$searchUrl?image_filter=store%2Cofficial&q=$name&page_size=25",
            ['headers' => ['Search-Version' => 'v3']]
        );
        $dataOfficial = new Json($responseOfficial->getContent());

        $response = array_merge(
            $dataOfficial->getData('summaries') ?? [],
            $dataCommunity->getData('summaries') ?? []
        );

        return $response;
    }

}
