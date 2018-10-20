<?php

namespace App\Tests\Builder;

use App\Builder\ContainerBuilder;
use App\Provider\ContainerProvider;
use App\Provider\Github;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ContainerBuilderTest extends TestCase
{
    /** @var MockObject|Github */
    private $mockGithub;
    /** @var MockObject|ContainerProvider */
    private $mockContainer;
    /** @var ContainerBuilder */
    private $builder;

    protected function setUp()
    {
        $this->mockGithub = $this->createMock(Github::class);
        $this->mockContainer = $this->createMock(ContainerProvider::class);

        $this->builder = new ContainerBuilder($this->mockGithub, $this->mockContainer);
    }

    public function testManifestWithoutFiles()
    {
        $this->mockContainer->method('getManifest')->willReturn([]);
        $container = $this->builder->build('name', []);

        $this->assertEmpty($container);
    }

    public function testManifestNotFound(){
//        $this->mockContainer->method('getManifest');
    }


}
