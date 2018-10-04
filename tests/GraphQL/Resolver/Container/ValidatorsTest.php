<?php

namespace App\Test\GraphQL\Resolver\Container;

use App\GraphQL\Resolver\Container\Validators;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ValidatorsTest extends WebTestCase
{
    private $resolver;
    private $fixturesPath;

    public static function setUpBeforeClass()
    {
        self::bootKernel();
    }

    protected function setUp()
    {
        $this->resolver = new Validators();
        $this->fixturesPath = self::$kernel->getRootDir() . '/../tests/GraphQL/Resolver/Container/Fixtures/';
    }

    public function testValidators()
    {
        $container = ['validators' => [
            'required' => true,
            'regex' => "^[a-z]+$"
        ]];

        $validators = call_user_func($this->resolver, $container);

        $this->assertCount(2, $validators);
        $this->assertSame(['name' => 'required', 'constraint' => true], $validators[0]);
        $this->assertSame(['name' => 'regex', 'constraint' => "^[a-z]+$"], $validators[1]);
    }

    public function testValidatorArray()
    {
        $container = ['validators' => [
            'number' => ['min' => 1, 'max' => 12],
        ]];

        $validators = call_user_func($this->resolver, $container);

        $this->assertCount(1, $validators);
        $this->assertSame(['name' => 'number', 'constraint' => '{"min":1,"max":12}'], $validators[0]);
    }

    public function testNoValidators()
    {
        $validators = call_user_func($this->resolver, []);

        $this->assertCount(0, $validators);
    }
}
