<?php

namespace App\Tests\Provider\Config;

use App\GraphQL\Resolver\Template\Templates;
use App\Provider\Template;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TemplatesTest extends WebTestCase
{
    /** @var Template|MockObject */
    private $mockProvider;
    private $resolver;
    private $fixturesPath;

    public static function setUpBeforeClass()
    {
        self::bootKernel();
    }

    protected function setUp()
    {
        $this->mockProvider = $this->createMock(Template::class);
        $this->resolver = new Templates($this->mockProvider);
        $this->fixturesPath = self::$kernel->getRootDir() . '/../tests/GraphQL/Resolver/Template/Fixtures/';
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
}
