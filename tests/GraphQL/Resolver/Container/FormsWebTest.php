<?php

namespace App\Tests\GraphQL\Resolver\Container;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class FormsWebTest extends WebTestCase
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
        $this->client->request(
            'POST',
            '/graphql/',
            ['query' => "{container_forms{id}}"]);

        $containerForms = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertEmpty($containerForms['errors'] ?? [], $containerForms['errors'][0]['message'] ?? '');
        $this->assertNotEmpty($containerForms['data']['container_forms'] ?? [], 'No container found');
    }

    public function testGetAllFields()
    {
        $this->client->request(
            'POST',
            '/graphql/',
            ['query' => '{
              container_forms {
                id
                name
                logo
                config {
                  id
                  label
                  fields {
                    id
                    label
                    type
                    base
                    destination
                    value
                    source {
                      label
                      value
                    }
                    validators {
                      name
                      constraint
                    }
                  }
                }
              }
            }']);

        $containerForms = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertEmpty($containerForms['errors'] ?? [], 'Errors entry not empty');
        $this->assertNotEmpty($containerForms['data']['container_forms'] ?? [], 'No container found');
    }
}
