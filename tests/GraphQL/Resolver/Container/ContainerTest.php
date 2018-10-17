<?php

namespace App\Tests\GraphQL\Resolver\Container;

use App\GraphQL\Resolver\Container\Container;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContainerTest extends WebTestCase
{
    /** @var \App\Provider\Container|MockObject */
    private $mockProvider;
    private $resolver;
    private $fixturesPath;

    public static function setUpBeforeClass()
    {
        self::bootKernel();
    }

    protected function setUp()
    {
        $this->mockProvider = $this->createMock(\App\Provider\Container::class);
        $this->resolver = new Container($this->mockProvider);
        $this->fixturesPath = self::$kernel->getRootDir() . '/../tests/GraphQL/Resolver/Container/Fixtures/';
    }

    public function testInvoke()
    {
        /** @noinspection PhpIncludeInspection */
        $adminerContainer = include $this->fixturesPath . 'adminer.php';
        $this->mockProvider->method('getFormConfig')->willReturn($adminerContainer);

        $container = call_user_func($this->resolver, 'adminer');
        $this->assertSame($adminerContainer, $container);
    }
}