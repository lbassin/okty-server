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
    private $mockProvider;
    /** @var ContainerBuilder */
    private $builder;

    private $fixturePath;

    protected function setUp()
    {
        $this->mockGithub = $this->createMock(Github::class);
        $this->mockProvider = $this->createMock(ContainerProvider::class);

        $this->builder = new ContainerBuilder($this->mockGithub, $this->mockProvider);

        $this->fixturePath = __DIR__ . '/Fixtures/';
    }

    public function testManifestWithoutFiles()
    {
        $this->mockProvider->method('getManifest')->willReturn([]);
        $files = $this->builder->build('name', []);

        $this->assertEmpty($files);
    }

    public function testManifestNotFound()
    {
        $exception = new FileNotFoundException('');
        $this->mockProvider->method('getManifest')->willThrowException($exception);

        $this->expectException(FileNotFoundException::class);

        $this->builder->build('', []);
    }

    public function testWithoutResolvers()
    {
        $this->mockProvider->method('getResolvers')->willReturn('');
        $files = $this->builder->build('', []);

        $this->assertEmpty($files);
    }

    public function testSourceNotFound()
    {
        /** @noinspection PhpIncludeInspection */
        $manifest = include $this->fixturePath . 'manifest.php';
        $this->mockProvider->method('getManifest')->willReturn($manifest);

        $exception = new FileNotFoundException('');

        $this->mockGithub
            ->expects($this->exactly(2))
            ->method('getFile')
            ->willThrowException($exception);

        $files = $this->builder->build('', []);

        $this->assertEmpty($files);
    }

    public function testNoResolversNoArgs()
    {
        /** @noinspection PhpIncludeInspection */
        $manifest = include $this->fixturePath . 'manifest.php';
        $this->mockProvider->method('getManifest')->willReturn($manifest);
        $this->mockProvider->method('getResolvers')->willReturn('');

        $dockerfile = file_get_contents($this->fixturePath . 'Dockerfile');
        $defaultConf = file_get_contents($this->fixturePath . 'default.conf');

        $this->mockGithub
            ->expects($this->exactly(2))
            ->method('getFile')
            ->willReturnOnConsecutiveCalls($dockerfile, $defaultConf);

        $files = $this->builder->build('nginx');

        $defaultConfProcessed = file_get_contents($this->fixturePath . 'noResolversNoArgs.default.conf');

        $this->assertCount(2, $files);

        $this->assertSame('./Dockerfile', $files[0]['name']);
        $this->assertSame($dockerfile, $files[0]['content']);

        $this->assertSame('./nginx/default.conf', $files[1]['name']);
        $this->assertSame($defaultConfProcessed, $files[1]['content']);
    }

    public function testNoResolversWithArgs()
    {
        /** @noinspection PhpIncludeInspection */
        $manifest = include $this->fixturePath . 'manifest.php';
        $this->mockProvider->method('getManifest')->willReturn($manifest);
        $this->mockProvider->method('getResolvers')->willReturn('');

        $dockerfile = file_get_contents($this->fixturePath . 'Dockerfile');
        $defaultConf = file_get_contents($this->fixturePath . 'default.conf');

        $this->mockGithub
            ->expects($this->exactly(2))
            ->method('getFile')
            ->willReturnOnConsecutiveCalls($dockerfile, $defaultConf);

        $files = $this->builder->build('nginx', [
            'root_folder' => 'public',
            'php_container_link' => 'mon_container_php'
        ]);

        $defaultConfProcessed = file_get_contents($this->fixturePath . 'noResolversWithArgs.default.conf');

        $this->assertCount(2, $files);

        $this->assertSame('./Dockerfile', $files[0]['name']);
        $this->assertSame($dockerfile, $files[0]['content']);

        $this->assertSame('./nginx/default.conf', $files[1]['name']);
        $this->assertSame($defaultConfProcessed, $files[1]['content']);
    }

    public function testWithResolversWithArgs()
    {
        /** @noinspection PhpIncludeInspection */
        $manifest = include $this->fixturePath . 'manifest.php';
        $this->mockProvider->method('getManifest')->willReturn($manifest);

        $resolvers = file_get_contents($this->fixturePath . 'resolvers.php');
        $this->mockProvider->method('getResolvers')->willReturn($resolvers);

        $dockerfile = file_get_contents($this->fixturePath . 'Dockerfile');
        $defaultConf = file_get_contents($this->fixturePath . 'default.conf');

        $this->mockGithub
            ->expects($this->exactly(2))
            ->method('getFile')
            ->willReturnOnConsecutiveCalls($dockerfile, $defaultConf);

        $files = $this->builder->build('nginx', [
            'root_folder' => 'public',
            'php_container_link' => 'php_id'
        ]);

        $defaultConfProcessed = file_get_contents($this->fixturePath . 'withResolversWithArgs.default.conf');

        $this->assertCount(2, $files);

        $this->assertSame('./Dockerfile', $files[0]['name']);
        $this->assertSame($dockerfile, $files[0]['content']);

        $this->assertSame('./nginx/default.conf', $files[1]['name']);
        $this->assertSame($defaultConfProcessed, $files[1]['content']);
    }

    protected function tearDown()
    {
        $this->builder = null;
        $this->mockGithub = null;
        $this->mockProvider = null;
    }

}
