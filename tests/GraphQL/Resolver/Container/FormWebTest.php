<?php

namespace App\Tests\GraphQL\Resolver\Container;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class FormWebTest extends WebTestCase
{
    /** @var Client $client */
    private $client;

    protected function setUp()
    {
        $this->client = static::createClient();
        static::$container->get('cache.app')->clear();
    }

    public function testListAllContainersForm()
    {
        /** @var Client $client */
        $this->client = static::createClient();

        $this->client->request(
            'POST',
            '/graphql/',
            ['query' => '{container_form(id: "nginx"){id}}']);

        $containerForms = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertEmpty($containerForms['errors'] ?? [], 'Errors entry not empty');
        $this->assertNotEmpty($containerForms['data']['container_form'] ?? [], 'No container found');
    }
}
