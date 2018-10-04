<?php

namespace App\Tests\Provider\Config;

use App\GraphQL\Resolver\Template\Template;
use App\Provider\Config\ConfigProvider;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TemplateTest extends WebTestCase
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
        $this->resolver = new Template($this->mockConfig);
        $this->fixturesPath = self::$kernel->getRootDir() . '/../tests/GraphQL/Resolver/Template/Fixtures/';
    }

    public function testInvoke()
    {
        /** @noinspection PhpIncludeInspection */
        $laravelTemplate = include $this->fixturesPath . 'laravel5.php';
        $this->mockConfig->method('getTemplate')->willReturn($laravelTemplate);

        $container = call_user_func($this->resolver, 'laravel5');
        $this->assertSame($laravelTemplate, $container);
    }
}
