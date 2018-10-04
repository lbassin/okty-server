<?php

namespace App\Tests\GraphQL\Resolver\Container;

use App\GraphQL\Resolver\Container\Container;
use App\Provider\Config\ConfigProvider;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContainerTest extends WebTestCase
{
    /** @var ConfigProvider|MockObject */
    private $mockConfig;
    private $resolver;
    private $fixturesPath;

    public static function setUpBeforeClass()
    {
        self::bootKernel();
    }

    protected function setUp()
    {
        $this->mockConfig = $this->createMock(ConfigProvider::class);
        $this->resolver = new Container($this->mockConfig);
        $this->fixturesPath = self::$kernel->getRootDir() . '/../tests/GraphQL/Resolver/Container/Fixtures/';
    }

    public function testInvoke()
    {
        /** @noinspection PhpIncludeInspection */
        $adminerContainer = include $this->fixturesPath . 'adminer.php';
        $this->mockConfig->method('getContainer')->willReturn($adminerContainer);

        $container = call_user_func($this->resolver, 'adminer');
        $this->assertSame($adminerContainer, $container);
    }
}