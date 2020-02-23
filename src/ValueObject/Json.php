<?php

declare(strict_types=1);

namespace App\ValueObject;

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

    public function getAsArray(): array
    {
        return $this->value;
    }
}
