<?php

namespace App\Tests\Provider;

use App\Provider\Container;
use App\Provider\Github;
use Github\Exception\RuntimeException;
use GraphQL\Error\ClientAware;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TemplateTest extends WebTestCase
{
    /** @var Container */
    private $container;
    /** @var MockObject|Github */
    private $mockGithub;

    public static function setUpBeforeClass()
    {
        static::bootKernel();
    }

    protected function setUp()
    {
        $this->mockGithub = $this->createMock(Github::class);
        $this->container = new Container($this->mockGithub, '');
    }

    private function getTwoContainers()
    {
        $apiResponse = [
            ["name" => "adminer.yml", "path" => "config/containers/adminer.yml", "size" => "634", "type" => "file"],
            ["name" => "nginx.yml", "path" => "config/containers/nginx.yml", "size" => "1527", "type" => "file"],
        ];

        $fixturesPath = self::$kernel->getRootDir() . '/../tests/Provider/Fixtures/';
        $adminerContainer = ["content" => base64_encode(file_get_contents($fixturesPath . 'adminer.yml'))];
        $nginxContainer = ["content" => base64_encode(file_get_contents($fixturesPath . 'nginx.yml'))];

        $this->mockGithub
            ->expects($this->once())
            ->method('getTree')
            ->willReturn($apiResponse);

        $this->mockGithub
            ->expects($this->exactly(2))
            ->method('getFile')
            ->willReturnOnConsecutiveCalls($adminerContainer, $nginxContainer);

        return $this->container->getAll();
    }

    private function getOneTemplate()
    {
        $apiResponse = [
            ["name" => "symfony4.yml", "path" => "config/templates/adminer.yml", "size" => "749", "type" => "file"],
        ];

        $fixturesPath = self::$kernel->getRootDir() . '/../tests/Provider/Fixtures/';
        $symfonyTemplate = ["content" => base64_encode(file_get_contents($fixturesPath . 'symfony4.yml'))];

        $this->mockGithub
            ->expects($this->once())
            ->method('getTree')
            ->willReturn($apiResponse);

        $this->mockGithub
            ->expects($this->once())
            ->method('getFile')
            ->willReturn($symfonyTemplate);

        return $this->container->getAll();
    }

    public function testGetAllElementsEmpty()
    {
        $this->mockGithub->method('getTree')->willReturn([]);

        $this->assertEmpty($this->container->getAll());
        $this->assertEmpty($this->container->getAllTemplates());
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

    public function testGetContainer()
    {
        $fixturesPath = self::$kernel->getRootDir() . '/../tests/Provider/Fixtures/';
        $adminerContainer = ["content" => base64_encode(file_get_contents($fixturesPath . 'adminer.yml'))];

        $this->mockGithub
            ->expects($this->once())
            ->method('getFile')->willReturn($adminerContainer);

        $container = $this->container->getContainer('adminer');

        $this->assertSame('Adminer', $container['name']);
        $this->assertCount(2, $container['config'][0]['fields']);
        $this->assertCount(7, $container['config'][0]['fields'][0]);
        $this->assertCount(2, $container['config'][0]['fields'][0]['validators']);
    }

    public function testGetTemplate()
    {
        $fixturesPath = self::$kernel->getRootDir() . '/../tests/Provider/Fixtures/';
        $symfonyTemplate = ["content" => base64_encode(file_get_contents($fixturesPath . 'symfony4.yml'))];

        $this->mockGithub
            ->expects($this->once())
            ->method('getFile')->willReturn($symfonyTemplate);

        $template = $this->container->getTemplate('symfony');

        $this->assertSame('Symfony 4', $template['name']);
        $this->assertCount(4, $template['containers']);
        $this->assertCount(7, $template['containers'][0]['config']);
    }

    public function testGetElementNotFound()
    {
        $exception = new RuntimeException();
        $this->mockGithub->method('getFile')->willThrowException($exception);

        $this->expectException(ClientAware::class);
        $this->container->getTemplate('non');
    }

    public function testGetElementsNotFound()
    {
        $exception = new RuntimeException();
        $this->mockGithub->method('getTree')->willThrowException($exception);

        $this->expectException(ClientAware::class);
        $this->container->getAllTemplates();
    }

    protected function tearDown()
    {
        $this->container = null;
        $this->mockContents = null;
        $this->mockRepo = null;
        $this->mockClient = null;
    }
}
