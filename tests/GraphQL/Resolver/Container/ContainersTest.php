<?php

namespace App\Tests\GraphQL\Resolver\Container;

use App\GraphQL\Resolver\Container\Forms;
use App\Provider\ContainerProvider;
use GraphQL\Error\ClientAware;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class ContainersTest extends TestCase
{
    /** @var ContainerProvider|MockObject */
    private $mockProvider;
    private $resolver;
    private $fixturesPath;

    protected function setUp()
    {
        $this->mockProvider = $this->createMock(ContainerProvider::class);
        $this->resolver = new Forms($this->mockProvider);
        $this->fixturesPath = __DIR__ . '/Fixtures/';
    }

    public function testInvoke()
    {
        /** @noinspection PhpIncludeInspection */
        $adminerContainer = include $this->fixturesPath . 'adminer.php';
        /** @noinspection PhpIncludeInspection */
        $nginxContainer = include $this->fixturesPath . 'nginx.php';

        $this->mockProvider->method('getAll')->willReturn([$adminerContainer, $nginxContainer]);

        $containers = call_user_func($this->resolver);

        $this->assertCount(2, $containers);
        $this->assertSame($adminerContainer, $containers[0]);
        $this->assertSame($nginxContainer, $containers[1]);
    }

    public function testNotFound()
    {
        $exception = new FileNotFoundException('');
        $this->mockProvider->method('getAll')->willThrowException($exception);

        $this->expectException(ClientAware::class);

        call_user_func($this->resolver, '');
    }
}
