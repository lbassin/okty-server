<?php

declare(strict_types=1);

use App\ValueObject\Json;
use PHPUnit\Framework\TestCase;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class JsonTest extends TestCase
{
    public function testValidJson()
    {
        $json = new Json('{"test": 42}');

        $this->assertInternalType('array', $json->getValue());
    }

    public function testInvalidFormat()
    {
        $this->expectException(LogicException::class);

        new Json('Not a json');
    }

    public function testEmptyJson()
    {
        $json = new Json('{}');

        $this->assertInternalType('array', $json->getValue());
    }

    public function testGetDataByKey()
    {
        $json = new Json('{"test": 42}');

        $this->assertEquals(42, $json->getData('test'));
    }

    public function testGetComplexDataByKey()
    {
        $json = new Json('{"test": 42, "args": {"image": "php"}}');

        $this->assertSame(['image' => 'php'], $json->getData('args'));
    }
}
