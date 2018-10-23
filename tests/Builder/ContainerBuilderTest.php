<?php

namespace App\Tests\Builder;

use App\Builder\ContainerBuilder;
use App\Provider\ContainerProvider;
use App\Provider\Github;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Yaml\Yaml;

class ContainerBuilderTest extends TestCase
{
    /** @var MockObject|Github */
    private $mockGithub;
    /** @var MockObject|ContainerProvider */
    private $mockProvider;
    /** @var MockObject|ValidatorInterface */
    private $mockValidator;
    /** @var ContainerBuilder */
    private $builder;

    private $fixturePath;

    protected function setUp()
    {
        $this->mockGithub = $this->createMock(Github::class);
        $this->mockProvider = $this->createMock(ContainerProvider::class);
        $this->mockValidator = $this->createMock(ValidatorInterface::class);

        $this->builder = new ContainerBuilder($this->mockGithub, $this->mockProvider, $this->mockValidator);

        $this->fixturePath = __DIR__ . '/Fixtures/';
    }

    public function testManifestWithoutFiles()
    {
        $this->mockProvider->method('getManifest')->willReturn([]);
        $files = $this->builder->build('ngninx', []);

        $this->assertCount(1, $files);
        $this->assertSame('docker-compose.yml', $files[0]['name']);
    }

    public function testManifestNotFound()
    {
        $exception = new FileNotFoundException('manifest.yml');
        $this->mockProvider->method('getManifest')->willThrowException($exception);

        $warnings = [];
        $files = $this->builder->build('', [], $warnings);

        $this->assertEmpty($files);
        $this->assertCount(2, $warnings);
        $this->assertSame('The file "manifest.yml" does not exist', $warnings[0]);
    }

    public function testWithoutResolvers()
    {
        $this->mockProvider->method('getResolvers')->willReturn('');

        $warnings = [];
        $files = $this->builder->build('', [], $warnings);

        $this->assertCount(1, $files);
        $this->assertCount(0, $warnings);
    }

    public function testSourceNotFound()
    {
        /** @noinspection PhpIncludeInspection */
        $manifest = include $this->fixturePath . 'manifest.php';
        $this->mockProvider->method('getManifest')->willReturn($manifest);

        $exception = new FileNotFoundException('file');

        $this->mockGithub
            ->expects($this->exactly(2))
            ->method('getFile')
            ->willThrowException($exception);

        $warnings = [];
        $files = $this->builder->build('', [], $warnings);

        $this->assertEmpty($files);
        $this->assertCount(2, $warnings);
        $this->assertSame('The file "file" does not exist', $warnings[0]);
    }

    public function testNoResolversNoArgs()
    {
        /** @noinspection PhpIncludeInspection */
        $manifest = include $this->fixturePath . 'manifest.php';
        $this->mockProvider->method('getManifest')->willReturn($manifest);
        $this->mockProvider->method('getResolvers')->willReturn('');

        $dockerfile = file_get_contents($this->fixturePath . 'Dockerfile');
        $defaultConf = file_get_contents($this->fixturePath . 'default.conf');
        $dockerCompose = YAML::parse(file_get_contents($this->fixturePath . 'docker-compose.yml'));

        $this->mockGithub
            ->expects($this->exactly(2))
            ->method('getFile')
            ->willReturnOnConsecutiveCalls($dockerfile, $defaultConf);

        $files = $this->builder->build('nginx');

        $defaultConfProcessed = file_get_contents($this->fixturePath . 'noResolversNoArgs.default.conf');

        $this->assertCount(3, $files);

        $this->assertSame('docker-compose.yml', $files[0]['name']);
        $this->assertSame($dockerCompose, YAML::parse($files[0]['content']));

        $this->assertSame('docker/nginx/Dockerfile', $files[1]['name']);
        $this->assertSame($dockerfile, $files[1]['content']);

        $this->assertSame('docker/nginx/default.conf', $files[2]['name']);
        $this->assertSame($defaultConfProcessed, $files[2]['content']);
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
            'files' => [
                'root_folder' => 'public',
                'php_container_link' => 'mon_container_php'
            ]
        ]);

        $defaultConfProcessed = file_get_contents($this->fixturePath . 'noResolversWithArgs.default.conf');

        $this->assertCount(3, $files);

        $this->assertSame('docker/nginx/Dockerfile', $files[1]['name']);
        $this->assertSame($dockerfile, $files[1]['content']);

        $this->assertSame('docker/nginx/default.conf', $files[2]['name']);
        $this->assertSame($defaultConfProcessed, $files[2]['content']);
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
            'files' => [
                'root_folder' => 'public',
                'php_container_link' => 'php_id'
            ]
        ]);

        $defaultConfProcessed = file_get_contents($this->fixturePath . 'withResolversWithArgs.default.conf');

        $this->assertCount(3, $files);

        $this->assertSame('docker/nginx/Dockerfile', $files[1]['name']);
        $this->assertSame($dockerfile, $files[1]['content']);

        $this->assertSame('docker/nginx/default.conf', $files[2]['name']);
        $this->assertSame($defaultConfProcessed, $files[2]['content']);
    }

    public function testManifestNoFiles()
    {
        /** @noinspection PhpIncludeInspection */
        $manifest = include $this->fixturePath . 'manifest-no-files.php';
        $this->mockProvider->method('getManifest')->willReturn($manifest);

        $files = $this->builder->build('nginx');

        $this->assertCount(1, $files);
    }

    public function testImageBuild()
    {
        /** @noinspection PhpIncludeInspection */
        $manifest = include $this->fixturePath . 'manifest-build.php';
        $this->mockProvider->method('getManifest')->willReturn($manifest);

        $files = $this->builder->build('nginx');
        $content = YAML::parse($files[0]['content']);


        $this->assertCount(1, $files);
        $this->assertSame('docker/nginx/', $content['services']['nginx']['build']);
    }

    public function testImageNoTag()
    {
        /** @noinspection PhpIncludeInspection */
        $manifest = include $this->fixturePath . 'manifest-image-no-tag.php';
        $this->mockProvider->method('getManifest')->willReturn($manifest);

        $files = $this->builder->build('nginx');
        $content = YAML::parse($files[0]['content']);

        $this->assertCount(1, $files);
        $this->assertSame('test:latest', $content['services']['nginx']['image']);
    }

    public function testImageWithTag()
    {
        /** @noinspection PhpIncludeInspection */
        $manifest = include $this->fixturePath . 'manifest-image-tag.php';
        $this->mockProvider->method('getManifest')->willReturn($manifest);

        $files = $this->builder->build('nginx');
        $content = YAML::parse($files[0]['content']);

        $this->assertCount(1, $files);
        $this->assertSame('test:3', $content['services']['nginx']['image']);
    }

    public function testImageWithTagWithArg()
    {
        /** @noinspection PhpIncludeInspection */
        $manifest = include $this->fixturePath . 'manifest-image-tag.php';
        $this->mockProvider->method('getManifest')->willReturn($manifest);

        $files = $this->builder->build('nginx', ['version' => '2']);
        $content = YAML::parse($files[0]['content']);

        $this->assertCount(1, $files);
        $this->assertSame('test:2', $content['services']['nginx']['image']);
    }

    public function testDockerComposePorts()
    {
        /** @noinspection PhpIncludeInspection */
        $manifest = include $this->fixturePath . 'manifest-image-tag.php';
        $this->mockProvider->method('getManifest')->willReturn($manifest);

        $this->mockValidator
            ->expects($this->exactly(2))
            ->method('validate')
            ->willReturn([]);

        $files = $this->builder->build('nginx', [
            'ports' => [
                '8080:80',
                '9000:22'
            ]
        ]);
        $content = YAML::parse($files[0]['content']);

        $this->assertCount(1, $files);
        $this->assertSame(['8080:80', '9000:22'], $content['services']['nginx']['ports']);
    }

    public function testDockerComposePortsInvalid()
    {
        /** @noinspection PhpIncludeInspection */
        $manifest = include $this->fixturePath . 'manifest-image-tag.php';
        $this->mockProvider->method('getManifest')->willReturn($manifest);

        $mockConstraint = $this->createMock(ConstraintViolation::class);
        $this->mockValidator
            ->expects($this->exactly(2))
            ->method('validate')
            ->willReturn([$mockConstraint]);

        $warnings = [];
        $files = $this->builder->build('nginx', [
            'ports' => [
                '8080:80',
                '9000:22'
            ]
        ], $warnings);

        $this->assertCount(0, $files);
        $this->assertCount(2, $warnings);
    }

    public function testDockerComposeVolumes()
    {
        /** @noinspection PhpIncludeInspection */
        $manifest = include $this->fixturePath . 'manifest-image-tag.php';
        $this->mockProvider->method('getManifest')->willReturn($manifest);

        $this->mockValidator
            ->expects($this->once())
            ->method('validate')
            ->willReturn([]);

        $files = $this->builder->build('nginx', ['volumes' => ['./:/app']]);
        $content = YAML::parse($files[0]['content']);

        $this->assertCount(1, $files);
        $this->assertSame(['./:/app'], $content['services']['nginx']['volumes']);
    }


    public function testDockerComposeVolumesInvalid()
    {
        /** @noinspection PhpIncludeInspection */
        $manifest = include $this->fixturePath . 'manifest-image-tag.php';
        $this->mockProvider->method('getManifest')->willReturn($manifest);

        $mockConstraint = $this->createMock(ConstraintViolation::class);
        $this->mockValidator
            ->expects($this->exactly(1))
            ->method('validate')
            ->willReturn([$mockConstraint]);

        $warnings = [];
        $files = $this->builder->build('nginx', ['volumes' => ['./:/app']], $warnings);

        $this->assertCount(0, $files);
        $this->assertCount(1, $warnings);
    }

    public function testDockerComposeEnvironments()
    {
        /** @noinspection PhpIncludeInspection */
        $manifest = include $this->fixturePath . 'manifest-image-tag.php';
        $this->mockProvider->method('getManifest')->willReturn($manifest);

        $this->mockValidator
            ->expects($this->once())
            ->method('validate')
            ->willReturn([]);

        $files = $this->builder->build('nginx', ['environments' => ['TEST=12']]);
        $content = YAML::parse($files[0]['content']);

        $this->assertCount(1, $files);
        $this->assertSame(['TEST=12'], $content['services']['nginx']['environments']);
    }


    public function testDockerComposeEnvironmentsInvalid()
    {
        /** @noinspection PhpIncludeInspection */
        $manifest = include $this->fixturePath . 'manifest-image-tag.php';
        $this->mockProvider->method('getManifest')->willReturn($manifest);

        $mockConstraint = $this->createMock(ConstraintViolation::class);
        $this->mockValidator
            ->expects($this->once())
            ->method('validate')
            ->willReturn([$mockConstraint]);

        $warnings = [];
        $files = $this->builder->build('nginx', ['environments' => ['TEST=12']], $warnings);

        $this->assertCount(0, $files);
        $this->assertCount(1, $warnings);
    }

    protected function tearDown()
    {
        $this->builder = null;
        $this->mockGithub = null;
        $this->mockProvider = null;
    }

}
