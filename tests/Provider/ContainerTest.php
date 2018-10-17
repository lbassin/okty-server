<?php

namespace App\Tests\Provider;

use App\Provider\Container;
use App\Provider\Github;
use Github\Exception\RuntimeException;
use GraphQL\Error\ClientAware;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    /** @var Container */
    private $provider;
    /** @var MockObject|Github */
    private $mockGithub;

    private $fixturesPath;

    protected function setUp()
    {
        $this->mockGithub = $this->createMock(Github::class);
        $this->provider = new Container($this->mockGithub, '');
        $this->fixturesPath = __DIR__ . '/Fixtures/';
    }

    private function getTwoContainers()
    {
        $apiResponse = [
            ["name" => "adminer.yml", "path" => "config/containers/adminer.yml", "size" => "634", "type" => "file"],
            ["name" => "nginx.yml", "path" => "config/containers/nginx.yml", "size" => "1527", "type" => "file"],
        ];

        $adminerContainer = ["content" => base64_encode(file_get_contents($this->fixturesPath . 'adminer.yml'))];
        $nginxContainer = ["content" => base64_encode(file_get_contents($this->fixturesPath . 'nginx.yml'))];

        $this->mockGithub
            ->expects($this->once())
            ->method('getTree')
            ->willReturn($apiResponse);

        $this->mockGithub
            ->expects($this->exactly(2))
            ->method('getFile')
            ->willReturnOnConsecutiveCalls($adminerContainer, $nginxContainer);

        return $this->provider->getAll();
    }

    public function testGetAllEmpty()
    {
        $this->mockGithub->method('getTree')->willReturn([]);

        $this->assertEmpty($this->provider->getAll());
    }

    public function testGetAllBasicData()
    {
        $containers = $this->getTwoContainers();
        $adminer = $containers[0];
        $nginx = $containers[1];

        $this->assertSame('Adminer', $adminer['name']);
        $this->assertSame('latest', $adminer['version']);

        $this->assertSame('okty/nginx', $nginx['docker']);
        $this->assertSame('https://cdn.worldvectorlogo.com/logos/nginx.svg', $nginx['image']);
    }

    public function testGetAllContainersConfig()
    {
        $containers = $this->getTwoContainers();

        $this->assertCount(1, $containers[0]['config']);
        $this->assertCount(3, $containers[0]['config'][0]['fields']);

        $this->assertSame('container_id', $containers[1]['config'][0]['fields'][0]['base']);
        $this->assertCount(2, $containers[1]['config'][1]['fields'][0]['validators']);
    }

    public function testGetContainer()
    {
        $adminerContainer = ["content" => base64_encode(file_get_contents($this->fixturesPath . 'adminer.yml'))];

        $this->mockGithub
            ->expects($this->once())
            ->method('getFile')->willReturn($adminerContainer);

        $container = $this->provider->getFormConfig('adminer');

        $this->assertSame('Adminer', $container['name']);
        $this->assertCount(3, $container['config'][0]['fields']);
        $this->assertCount(7, $container['config'][0]['fields'][0]);
        $this->assertCount(2, $container['config'][0]['fields'][0]['validators']);
        $this->assertCount(2, $container['config'][0]['fields'][2]['source']);
    }

    public function testGetElementNotFound()
    {
        $exception = new RuntimeException();
        $this->mockGithub->method('getFile')->willThrowException($exception);

        $this->expectException(ClientAware::class);
        $this->provider->getFormConfig('non');
    }

    public function testGetElementsNotFound()
    {
        $exception = new RuntimeException();
        $this->mockGithub->method('getTree')->willThrowException($exception);

        $this->expectException(ClientAware::class);
        $this->provider->getAll();
    }

    protected function tearDown()
    {
        $this->mockGithub = null;
    }
}
