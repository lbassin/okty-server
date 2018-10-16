<?php

namespace App\Tests\Provider;

use App\Entity\Container\ConfigGroup;
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
        /** @var \App\Entity\Container[] $containers */
        $containers = $this->getTwoContainers();
        $adminer = $containers[0];
        $nginx = $containers[1];

        $this->assertSame('Adminer', $adminer->getName());
        $this->assertSame('latest', $adminer->getVersion());

        $this->assertSame('okty/nginx', $nginx->getDocker());
        $this->assertSame('https://cdn.worldvectorlogo.com/logos/nginx.svg', $nginx->getImage());
    }

    public function testGetAllContainersConfig()
    {
        $containers = $this->getTwoContainers();
        /** @var \App\Entity\Container $adminer */
        $adminer = $containers[0];
        /** @var ConfigGroup $config */
        $config = $adminer->getConfig()[0] ?? [];

        $this->assertCount(1, $adminer->getConfig());
//        $this->assertCount(2, $config->getFields());
//        $this->assertCount(2, $containers[0]['config'][0]['fields']);

//        $this->assertSame('container_id', $containers[1]['config'][0]['fields'][0]['base']);
//        $this->assertCount(2, $containers[1]['config'][1]['fields'][0]['validators']);
    }

    public function testGetContainer()
    {
        $adminerContainer = ["content" => base64_encode(file_get_contents($this->fixturesPath . 'adminer.yml'))];

        $this->mockGithub
            ->expects($this->once())
            ->method('getFile')->willReturn($adminerContainer);

        $container = $this->provider->getFormConfig('adminer');

        $this->assertSame('Adminer', $container->getName());
//        $this->assertCount(2, $container['config'][0]['fields']);
//        $this->assertCount(7, $container['config'][0]['fields'][0]);
//        $this->assertCount(2, $container['config'][0]['fields'][0]['validators']);
    }

    public function testGetElementNotFound()
    {
        $exception = new RuntimeException();
        $this->mockGithub->method('getFile')->willThrowException($exception);

        $this->expectException(ClientAware::class);
        $this->provider->getFormConfig('non');
    }

    protected function tearDown()
    {
        $this->mockGithub = null;
    }
}
