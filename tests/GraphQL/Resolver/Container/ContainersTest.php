<?php

namespace App\Tests\Provider\Config;

use App\GraphQL\Resolver\Container\Containers;
use App\Provider\Container;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContainersTest extends WebTestCase
{
    /** @var Container|MockObject */
    private $mockProvider;
    private $resolver;
    private $fixturesPath;

    public static function setUpBeforeClass()
    {
        self::bootKernel();
    }

    protected function setUp()
    {
        $this->mockProvider = $this->createMock(Container::class);
        $this->resolver = new Containers($this->mockProvider);
        $this->fixturesPath = self::$kernel->getRootDir() . '/../tests/GraphQL/Resolver/Container/Fixtures/';
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
}
