<?php

namespace App\Tests\Provider;

use App\Provider\ContainerProvider;
use App\Provider\Github;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class ContainerProviderTest extends TestCase
{
    /** @var ContainerProvider */
    private $provider;
    /** @var MockObject|Github */
    private $mockGithub;

    private $fixturesPath;

    protected function setUp()
    {
        $this->mockGithub = $this->createMock(Github::class);
        $this->provider = new ContainerProvider($this->mockGithub, '');
        $this->fixturesPath = __DIR__ . '/Fixtures/';
    }

    private function getTwoContainers()
    {
        $apiResponse = [
            ["name" => "adminer.yml", "path" => "config/containers/adminer.yml", "size" => "634", "type" => "file"],
            ["name" => "nginx.yml", "path" => "config/containers/nginx.yml", "size" => "1527", "type" => "file"],
        ];

        $adminerContainer = file_get_contents($this->fixturesPath . 'adminer.yml');
        $nginxContainer = file_get_contents($this->fixturesPath . 'nginx.yml');

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
        $adminerContainer = file_get_contents($this->fixturesPath . 'adminer.yml');

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

    public function testGetContainerNotFound()
    {
        $exception = new FileNotFoundException('');
        $this->mockGithub->method('getFile')->willThrowException($exception);

        $this->expectException(FileNotFoundException::class);
        $this->provider->getFormConfig('non');
    }

    public function testGetContainersNotFound()
    {
        $exception = new FileNotFoundException('');
        $this->mockGithub->method('getTree')->willThrowException($exception);

        $this->expectException(FileNotFoundException::class);
        $this->provider->getAll();
    }

    public function testGetManifest()
    {
        $manifestTest = file_get_contents($this->fixturesPath . 'manifest.yml');

        $this->mockGithub
            ->expects($this->once())
            ->method('getFile')
            ->willReturn($manifestTest);

        $manifest = $this->provider->getManifest('nginx');

        $this->assertCount(2, $manifest['files']);
        $this->assertCount(2, $manifest['config']);
        $this->assertCount(3, $manifest['config']['default.conf']['args']);
    }

    public function testGetManifestNotFound()
    {
        $exception = new FileNotFoundException('');
        $this->mockGithub->method('getFile')->willThrowException($exception);

        $this->expectException(FileNotFoundException::class);
        $this->provider->getManifest('non');
    }

    public function testGetResolvers()
    {
        $resolvers = file_get_contents($this->fixturesPath . 'resolvers.php');
        $this->mockGithub
            ->expects($this->once())
            ->method('getFile')
            ->willReturn($resolvers);

        $content = $this->provider->getResolvers('nginx');

        $headerRemoved = preg_match('/^<?php/', $content) ? false : true;
        $this->assertTrue($headerRemoved);
        $this->assertGreaterThan(1, strlen($content));
    }

    public function testGetResolversNotFound()
    {
        $exception = new FileNotFoundException('');
        $this->mockGithub->method('getFile')->willThrowException($exception);

        $content = $this->provider->getResolvers('nginx');

        $this->assertEmpty($content);
    }

    protected function tearDown()
    {
        $this->mockGithub = null;
    }
}
