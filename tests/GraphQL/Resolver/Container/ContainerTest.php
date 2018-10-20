<?php

namespace App\Tests\GraphQL\Resolver\Container;

use App\GraphQL\Resolver\Container\Container;
use GraphQL\Error\ClientAware;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class ContainerTest extends TestCase
{
    /** @var \App\Provider\ContainerProvider|MockObject */
    private $mockProvider;
    private $resolver;
    private $fixturesPath;

    protected function setUp()
    {
        $this->mockProvider = $this->createMock(\App\Provider\ContainerProvider::class);
        $this->resolver = new Container($this->mockProvider);
        $this->fixturesPath = __DIR__ . '/Fixtures/';
    }

    public function testInvoke()
    {
        /** @noinspection PhpIncludeInspection */
        $adminerContainer = include $this->fixturesPath . 'adminer.php';
        $this->mockProvider->method('getFormConfig')->willReturn($adminerContainer);

        $container = call_user_func($this->resolver, 'adminer');
        $this->assertSame($adminerContainer, $container);
    }

    public function testNotFound()
    {
        $exception = new FileNotFoundException('');
        $this->mockProvider->method('getFormConfig')->willThrowException($exception);

        $this->expectException(ClientAware::class);

        call_user_func($this->resolver, '');
    }
}