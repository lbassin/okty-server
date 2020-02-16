<?php

declare(strict_types=1);

namespace App\ValueObject;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Json
{
    private $value;

    public function __construct(string $json)
    {
        $value = json_decode($json, true);
        if (!$value) {
            throw new \LogicException('JSON Syntax Error');
        }

        $this->value = $value;
    }

    public function getValue(): array
    {
        return $this->value;
    }

    public function getData(string $key)
    {
        return $this->value[$key];
    }
}
