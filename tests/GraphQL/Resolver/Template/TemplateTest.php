<?php

namespace App\Tests\Provider\Config;

use App\GraphQL\Resolver\Template\Template;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TemplateTest extends WebTestCase
{
    /** @var \App\Provider\Template|MockObject */
    private $mockProvider;
    private $resolver;
    private $fixturesPath;

    public static function setUpBeforeClass()
    {
        self::bootKernel();
    }

    protected function setUp()
    {
        $this->mockProvider = $this->createMock(\App\Provider\Template::class);
        $this->resolver = new Template($this->mockProvider);
        $this->fixturesPath = self::$kernel->getRootDir() . '/../tests/GraphQL/Resolver/Template/Fixtures/';
    }

    public function testInvoke()
    {
        /** @noinspection PhpIncludeInspection */
        $laravelTemplate = include $this->fixturesPath . 'laravel5.php';
        $this->mockProvider->method('getOne')->willReturn($laravelTemplate);

        $container = call_user_func($this->resolver, 'laravel5');
        $this->assertSame($laravelTemplate, $container);
    }
}
