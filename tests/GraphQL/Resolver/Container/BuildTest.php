<?php

namespace App\Tests\GraphQL\Resolver\Container;

use App\Builder\ContainerBuilder;
use App\GraphQL\Resolver\Container\Build;
use App\Helper\ZipHelper;
use App\Provider\Cloud;
use GraphQL\Error\ClientAware;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class BuildTest extends TestCase
{
    private $resolver;
    /** @var MockObject|ContainerBuilder */
    private $mockBuilder;
    /** @var MockObject|ZipHelper */
    private $mockZipHelper;
    /** @var MockObject|Cloud */
    private $mockCloud;

    protected function setUp()
    {
        $this->mockBuilder = $this->createMock(ContainerBuilder::class);
        $this->mockZipHelper = $this->createMock(ZipHelper::class);
        $this->mockCloud = $this->createMock(Cloud::class);

        $this->resolver = new Build($this->mockBuilder, $this->mockZipHelper, $this->mockCloud);
    }

    public function testInvalidJson()
    {
        $args = '{test: 32';
        $this->expectException(ClientAware::class);

        call_user_func($this->resolver, $args);
    }

    public function testEmptyArgs()
    {
        $args = '';
        $this->expectException(ClientAware::class);

        call_user_func($this->resolver, $args);
    }

    public function testWrongJsonFormat()
    {
        $args = json_encode([
            ['name' => 'ok', 'data' => ['ok' => 1]]
        ]);
        $this->expectException(ClientAware::class);

        call_user_func($this->resolver, $args);
    }

    public function testUploadFail()
    {
        $args = json_encode([
            ['image' => 'nginx', 'args' => ['id' => 'web']]
        ]);

        $this->mockCloud
            ->method('upload')
            ->willThrowException(new AccessDeniedException('test'));

        $this->expectException(ClientAware::class);

        call_user_func($this->resolver, $args);
    }

    public function testHalfWrongHalfRight()
    {
        $args = json_encode([
            ['name' => 'ok', 'data' => ['ok' => 1]],
            ['image' => 'nginx', 'args' => ['id' => 'web']]
        ]);
        $this->expectException(ClientAware::class);

        $this->mockBuilder
            ->expects($this->once())
            ->method('buildAll');

        call_user_func($this->resolver, $args);
    }

    public function testValidData()
    {
        $args = json_encode([
            ['image' => 'nginx', 'args' => ['id' => 'web']]
        ]);

        $files = [['name' => 'docker-compose.yml', 'content' => '']];

        $this->mockBuilder
            ->expects($this->once())
            ->method('buildAll')
            ->with(json_decode($args, true))
            ->willReturn($files);

        $this->mockZipHelper
            ->expects($this->once())
            ->method('zip')
            ->with($files)
            ->willReturn('/tmp/generated/1234');

        $this->mockCloud
            ->expects($this->once())
            ->method('upload')
            ->with('/tmp/generated/1234')
            ->willReturn('http://download.com/5678');

        $url = call_user_func($this->resolver, $args);

        $this->assertSame('http://download.com/5678', $url);
    }

    protected function tearDown()
    {
        $this->mockBuilder = null;
        $this->mockZipHelper = null;
        $this->mockCloud = null;

        $this->resolver = null;
    }
}
