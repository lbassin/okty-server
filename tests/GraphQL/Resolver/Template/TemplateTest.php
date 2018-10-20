<?php

namespace App\Tests\GraphQL\Resolver\Template;

use App\GraphQL\Resolver\Template\Template;
use GraphQL\Error\ClientAware;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class TemplateTest extends TestCase
{
    /** @var \App\Provider\TemplateProvider|MockObject */
    private $mockProvider;
    private $resolver;
    private $fixturesPath;

    protected function setUp()
    {
        $this->mockProvider = $this->createMock(\App\Provider\TemplateProvider::class);
        $this->resolver = new Template($this->mockProvider);
        $this->fixturesPath = __DIR__ . '/Fixtures/';
    }

    public function testInvoke()
    {
        /** @noinspection PhpIncludeInspection */
        $laravelTemplate = include $this->fixturesPath . 'laravel5.php';
        $this->mockProvider->method('getOne')->willReturn($laravelTemplate);

        $container = call_user_func($this->resolver, 'laravel5');
        $this->assertSame($laravelTemplate, $container);
    }

    public function testNotFound()
    {
        $exception = new FileNotFoundException('');
        $this->mockProvider->method('getOne')->willThrowException($exception);

        $this->expectException(ClientAware::class);

        call_user_func($this->resolver, '');
    }
}
