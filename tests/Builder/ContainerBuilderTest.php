<?php

namespace App\Tests\Builder;

use App\Builder\ContainerBuilder;
use App\Provider\ContainerProvider;
use App\Provider\Github;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class ContainerBuilderTest extends TestCase
{
    /** @var MockObject|Github */
    private $mockGithub;
    /** @var MockObject|ContainerProvider */
    private $provider;
    /** @var ContainerBuilder */
    private $builder;

    private $fixturePath;

    protected function setUp()
    {
        $this->mockGithub = $this->createMock(Github::class);
        $this->provider = $this->createMock(ContainerProvider::class);

        $this->builder = new ContainerBuilder($this->mockGithub, $this->provider);

        $this->fixturePath = __DIR__ . '/Fixtures/';
    }

    public function testManifestWithoutFiles()
    {
        $this->provider->method('getManifest')->willReturn([]);
        $container = $this->builder->build('name', []);

        $this->assertEmpty($container);
    }

    public function testManifestNotFound()
    {
        $exception = new FileNotFoundException('');
        $this->provider->method('getManifest')->willThrowException($exception);

        $this->expectException(FileNotFoundException::class);

        $this->builder->build('', []);
    }

    public function testWithoutResolvers()
    {
        $this->provider->method('getResolvers')->willReturn('');
        $container = $this->builder->build('', []);

        $this->assertEmpty($container);
    }

    public function testSourceNotFound()
    {
        /** @noinspection PhpIncludeInspection */
        $manifest = include $this->fixturePath . 'manifest.php';
        $this->provider->method('getManifest')->willReturn($manifest);

        $exception = new FileNotFoundException('');

        $this->mockGithub
            ->expects($this->exactly(2))
            ->method('getFile')
            ->willThrowException($exception);

        $container = $this->builder->build('', []);

        $this->assertEmpty($container);
    }

}
