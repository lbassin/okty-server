<?php

namespace App\Tests\Provider;

use App\Provider\Github;
use App\Provider\TemplateProvider;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class TemplateProviderTest extends WebTestCase
{
    /** @var TemplateProvider */
    private $provider;
    /** @var MockObject|Github */
    private $mockGithub;

    private $fixturesPath;

    public static function setUpBeforeClass()
    {
        static::bootKernel();
    }

    protected function setUp()
    {
        $this->mockGithub = $this->createMock(Github::class);
        $this->provider = new TemplateProvider($this->mockGithub, '');
        $this->fixturesPath = __DIR__ . '/Fixtures/';
    }

    private function getOneTemplate()
    {
        $apiResponse = [
            ["name" => "symfony4.yml", "path" => "config/templates/adminer.yml", "size" => "749", "type" => "file"],
        ];

        $symfonyTemplate = file_get_contents($this->fixturesPath . 'symfony4.yml');

        $this->mockGithub
            ->expects($this->once())
            ->method('getTree')
            ->willReturn($apiResponse);

        $this->mockGithub
            ->expects($this->once())
            ->method('getFile')
            ->willReturn($symfonyTemplate);

        return $this->provider->getAll();
    }

    public function testGetAllElementsEmpty()
    {
        $this->mockGithub->method('getTree')->willReturn([]);

        $this->assertEmpty($this->provider->getAll());
    }

    public function testGetAllTemplatesBasicData()
    {
        $templates = $this->getOneTemplate();

        $this->assertSame('Symfony 4', $templates[0]['name']);
        $this->assertSame('https://cdn.worldvectorlogo.com/logos/symfony.svg', $templates[0]['image']);
    }

    public function testGetAllTemplatesContainers()
    {
        $templates = $this->getOneTemplate();

        $this->assertCount(4, $templates[0]['containers']);
        $this->assertCount(7, $templates[0]['containers'][0]['config']);

        $this->assertSame('adminer', $templates[0]['containers'][1]['configPath']);
        $this->assertSame('adminer', $templates[0]['containers'][1]['containerId']);
    }

    public function testGetTemplate()
    {
        $symfonyTemplate = file_get_contents($this->fixturesPath . 'symfony4.yml');

        $this->mockGithub
            ->expects($this->once())
            ->method('getFile')->willReturn($symfonyTemplate);

        $template = $this->provider->getOne('symfony');

        $this->assertSame('Symfony 4', $template['name']);
        $this->assertCount(4, $template['containers']);
        $this->assertCount(7, $template['containers'][0]['config']);
    }

    public function testGetElementNotFound()
    {
        $exception = new FileNotFoundException('');
        $this->mockGithub->method('getFile')->willThrowException($exception);

        $this->expectException(FileNotFoundException::class);
        $this->provider->getOne('non');
    }

    public function testGetElementsNotFound()
    {
        $exception = new FileNotFoundException('');
        $this->mockGithub->method('getTree')->willThrowException($exception);

        $this->expectException(FileNotFoundException::class);
        $this->provider->getAll();
    }

    protected function tearDown()
    {
        $this->mockGithub = null;
    }
}
