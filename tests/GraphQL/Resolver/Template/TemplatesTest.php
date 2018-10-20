<?php

namespace App\Tests\GraphQL\Resolver\Template;

use App\GraphQL\Resolver\Template\Templates;
use App\Provider\TemplateProvider;
use GraphQL\Error\ClientAware;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class TemplatesTest extends TestCase
{
    /** @var TemplateProvider|MockObject */
    private $mockProvider;
    private $resolver;
    private $fixturesPath;

    protected function setUp()
    {
        $this->mockProvider = $this->createMock(TemplateProvider::class);
        $this->resolver = new Templates($this->mockProvider);
        $this->fixturesPath = __DIR__ . '/Fixtures/';
    }

    public function testInvoke()
    {
        /** @noinspection PhpIncludeInspection */
        $symfonyTemplate = include $this->fixturesPath . 'symfony4.php';
        /** @noinspection PhpIncludeInspection */
        $laravelTemplate = include $this->fixturesPath . 'laravel5.php';

        $this->mockProvider->method('getAll')->willReturn([$symfonyTemplate, $laravelTemplate]);

        $containers = call_user_func($this->resolver);

        $this->assertCount(2, $containers);
        $this->assertSame($symfonyTemplate, $containers[0]);
        $this->assertSame($laravelTemplate, $containers[1]);
    }

    public function testNotFound()
    {
        $exception = new FileNotFoundException('');
        $this->mockProvider->method('getAll')->willThrowException($exception);

        $this->expectException(ClientAware::class);

        call_user_func($this->resolver, '');
    }
}
