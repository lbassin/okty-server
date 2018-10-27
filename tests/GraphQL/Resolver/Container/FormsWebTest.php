<?php

namespace App\Tests\GraphQL\Resolver\Container;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class FormsWebTest extends WebTestCase
{
    public function testListAllContainersForm()
    {
        /** @var Client $client */
        $client = static::createClient();

        $client->request(
            'POST',
            '/graphql/',
            ['query' => "{container_forms{id}}"]);

        $containerForms = json_decode($client->getResponse()->getContent(), true);

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertEmpty($containerForms['errors'] ?? [], 'Errors entry not empty');
        $this->assertNotEmpty($containerForms['data']['container_forms'] ?? [], 'No container found');
    }
}
