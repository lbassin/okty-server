<?php

namespace App\Tests\Provider\Config;

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

        self::$container->get('Psr\Cache\CacheItemPoolInterface')->clear();
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

        return $this->github->getAllContainers();
    }

    private function getOneTemplate()
    {
        $apiResponse = [
            ["name" => "symfony4.yml", "path" => "config/templates/adminer.yml", "size" => "749", "type" => "file"],
        ];

        $fixturesPath = self::$kernel->getRootDir() . '/../tests/Provider/Config/Fixtures/';
        $symfonyTemplate = ["content" => base64_encode(file_get_contents($fixturesPath . 'symfony4.yml'))];

        $this->mockContents
            ->expects($this->exactly(2))
            ->method('show')
            ->willReturnOnConsecutiveCalls($apiResponse, $symfonyTemplate);

        return $this->github->getAllTemplates();
    }

    public function testGetAllElementsEmpty()
    {
        $this->mockContents->method('show')->willReturn([]);

        $this->assertEmpty($this->github->getAllContainers());
        $this->assertEmpty($this->github->getAllTemplates());
    }

    public function testGetAllContainersBasicData()
    {
        $containers = $this->getTwoContainers();

        $this->assertSame('Adminer', $containers[0]['name']);
        $this->assertSame('latest', $containers[0]['version']);

        $this->assertSame('okty/nginx', $containers[1]['docker']);
        $this->assertSame('https://cdn.worldvectorlogo.com/logos/nginx.svg', $containers[1]['image']);
    }

    public function testGetAllTemplatesBasicData()
    {
        $templates = $this->getOneTemplate();

        $this->assertSame('Symfony 4', $templates[0]['name']);
        $this->assertSame('https://cdn.worldvectorlogo.com/logos/symfony.svg', $templates[0]['image']);
    }

    public function testGetAllContainersConfig()
    {
        $containers = $this->getTwoContainers();

        $this->assertCount(1, $containers[0]['config']);
        $this->assertCount(2, $containers[0]['config'][0]['fields']);

        $this->assertSame('container_id', $containers[1]['config'][0]['fields'][0]['base']);
        $this->assertCount(2, $containers[1]['config'][1]['fields'][0]['validators']);
    }

    public function testGetAllTemplatesContainers()
    {
        $templates = $this->getOneTemplate();

        $this->assertCount(4, $templates[0]['containers']);
        $this->assertCount(7, $templates[0]['containers'][0]['config']);

        $this->assertSame('adminer', $templates[0]['containers'][1]['configPath']);
        $this->assertSame('adminer', $templates[0]['containers'][1]['containerId']);
    }

    public function testGetContainersCache()
    {
        $apiResponse = [
            ["name" => "adminer.yml", "path" => "config/containers/adminer.yml", "size" => "634", "type" => "file"]
        ];

        $fixturesPath = self::$kernel->getRootDir() . '/../tests/Provider/Config/Fixtures/';
        $adminerContainer = ["content" => base64_encode(file_get_contents($fixturesPath . 'adminer.yml'))];

        $this->mockContents
            ->expects($this->exactly(3))
            ->method('show')
            ->willReturnOnConsecutiveCalls($apiResponse, $adminerContainer, $apiResponse);

        $this->github->getAllContainers();
        $containers = $this->github->getAllContainers();

        $this->assertCount(1, $containers);
    }

    public function testGetTemplatesCache()
    {
        $apiResponse = [
            ["name" => "symfony4.yml", "path" => "config/templates/adminer.yml", "size" => "749", "type" => "file"],
        ];

        $fixturesPath = self::$kernel->getRootDir() . '/../tests/Provider/Config/Fixtures/';
        $symfonyTemplate = ["content" => base64_encode(file_get_contents($fixturesPath . 'symfony4.yml'))];

        $this->mockContents
            ->expects($this->exactly(3))
            ->method('show')
            ->willReturnOnConsecutiveCalls($apiResponse, $symfonyTemplate, $apiResponse);

        $this->github->getAllTemplates();
        $containers = $this->github->getAllTemplates();

        $this->assertCount(1, $containers);
    }

    protected function tearDown()
    {
        $this->github = null;
        $this->mockContents = null;
        $this->mockRepo = null;
        $this->mockClient = null;
    }
}
