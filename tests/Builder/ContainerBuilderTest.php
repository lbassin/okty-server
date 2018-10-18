<?php

namespace App\Tests\Builder;

use App\Builder\ContainerBuilder;
use App\Provider\Container;
use App\Provider\Github;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ContainerBuilderTest extends TestCase
{
    /** @var MockObject|Github */
    private $mockGithub;
    /** @var MockObject|Container */
    private $mockContainer;
    /** @var ContainerBuilder */
    private $builder;

    protected function setUp()
    {
        $this->mockGithub = $this->createMock(Github::class);
        $this->mockContainer = $this->createMock(Container::class);

        $this->builder = new ContainerBuilder($this->mockGithub, $this->mockContainer);
    }
}
