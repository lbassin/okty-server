<?php

declare(strict_types=1);

namespace App\Entity;

use App\Exception\ValueObject\InvalidEnvNameException;

class Environment
{
    private $key;
    private $value;

    public function __construct(string $key, string $value)
    {
        if (!preg_match('/^[a-z][a-z0-9_]*$/i', $key)) {
            throw new InvalidEnvNameException($key);
        }

        $this->key = $key;
        $this->value = $value;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
