<?php

namespace App\Tests\Provider\Config;

use App\Entity\Container;
use App\Provider\Config\Github;
use Github\Api\Repo;
use Github\Api\Repository\Contents;
use Github\Client;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GithubTest extends WebTestCase
{
    /** @var Github */
    private $github;
    /** @var MockObject */
    private $mockRepo;
    /** @var MockObject */
    private $mockClient;
    /** @var MockObject */
    private $mockContents;

    protected function setUp()
    {
        $this->mockContents = $this->createMock(Contents::class);

        $this->mockRepo = $this->createMock(Repo::class);
        $this->mockRepo->method('contents')->willReturn($this->mockContents);

        $this->mockClient = $this->createMock(Client::class);
        $this->mockClient->method('api')->willReturn($this->mockRepo);

        self::bootKernel();
        self::$container->set('Github\Client', $this->mockClient);

        $this->github = self::$container->get('App\Provider\Config\Github');
    }

    private function getTwoContainers()
    {
        $apiResponse = [
            ["name" => "adminer.yml", "path" => "config/containers/adminer.yml", "size" => "634", "type" => "file"],
            ["name" => "nginx.yml", "path" => "config/containers/nginx.yml", "size" => "1527", "type" => "file"],
        ];

        $fixturesPath = self::$kernel->getRootDir() . '/../tests/Provider/Config/Fixtures/';
        $adminerContainer = ["content" => base64_encode(file_get_contents($fixturesPath . 'adminer.yml'))];
        $nginxContainer = ["content" => base64_encode(file_get_contents($fixturesPath . 'nginx.yml'))];

        $this->mockContents
            ->expects($this->exactly(3))
            ->method('show')
            ->willReturnOnConsecutiveCalls($apiResponse, $adminerContainer, $nginxContainer);

        /** @var Container[] $containers */
        $containers = $this->github->getAllContainers();

        return $containers;
    }

    public function testGetAllContainersEmpty()
    {
        $this->mockContents->method('show')->willReturn([]);

        $this->assertEmpty($this->github->getAllContainers());
    }

    public function testGetAllContainersBasicData()
    {
        $containers = $this->getTwoContainers();

        $this->assertSame('Adminer', $containers[0]['name']);
        $this->assertSame('latest', $containers[0]['version']);

        $this->assertSame('okty/nginx', $containers[1]['docker']);
        $this->assertSame('https://cdn.worldvectorlogo.com/logos/nginx.svg', $containers[1]['image']);
    }

    public function testGetAllContainersConfig()
    {
        $containers = $this->getTwoContainers();

        $this->assertCount(1, $containers[0]['config']);
        $this->assertCount(2, $containers[0]['config'][0]['fields']);

        $this->assertSame('container_id', $containers[1]['config'][0]['fields'][0]['base']);
        $this->assertCount(2, $containers[1]['config'][1]['fields'][0]['validators']);
    }

    protected function tearDown()
    {
        $this->github = null;
    }
}
