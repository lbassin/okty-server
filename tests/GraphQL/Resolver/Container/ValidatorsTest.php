<?php declare(strict_types=1);

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

    public function testNoValidators()
    {
        $validators = call_user_func($this->resolver, []);

        $this->assertCount(0, $validators);
    }
}
