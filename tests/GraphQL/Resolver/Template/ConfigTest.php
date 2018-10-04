<?php

namespace App\Tests\GraphQL\Resolver\Template;

use App\GraphQL\Resolver\Template\Config;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ConfigTest extends WebTestCase
{
    private $resolver;

    public static function setUpBeforeClass()
    {
        self::bootKernel();
    }

    protected function setUp()
    {
        $this->resolver = new Config();
    }

    public function testConfig()
    {
        $template = [
            'containers' => [
                ['config' => ['General_name' => 'mysql', 'General_port' => '3306']],
                ['config' => ['General_name' => 'nginx']]
            ]
        ];

        $config = [];
        foreach ($template['containers'] as $container) {
            $config[] = call_user_func($this->resolver, $container);
        }

        $this->assertCount(2, $config[0]);
        $this->assertCount(1, $config[1]);

        $this->assertSame([
            ['label' => 'General_name', 'value' => 'mysql'],
            ['label' => 'General_port', 'value' => '3306']
        ], $config[0]);

        $this->assertSame([['label' => 'General_name', 'value' => 'nginx']], $config[1]);
    }

    public function testNoConfig()
    {
        $config = call_user_func($this->resolver, []);

        $this->assertCount(0, $config);
    }
}
