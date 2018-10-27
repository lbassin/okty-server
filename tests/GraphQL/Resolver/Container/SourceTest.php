<?php

namespace App\Test\GraphQL\Resolver\Container;

use App\GraphQL\Resolver\Container\Source;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SourceTest extends WebTestCase
{
    private $resolver;

    protected function setUp()
    {
        $this->resolver = new Source();
    }

    public function testSource()
    {
        $container = ['source' => [
            'value1' => 'Label1',
            'value2' => 'Label2'
        ]];

        $source = call_user_func($this->resolver, $container);

        $this->assertCount(2, $source);
        $this->assertSame(['label' => 'Label1', 'value' => 'value1'], $source[0]);
        $this->assertSame(['label' => 'Label2', 'value' => 'value2'], $source[1]);
    }

    public function testNoValidators()
    {
        $source = call_user_func($this->resolver, []);

        $this->assertCount(0, $source);
    }
}
