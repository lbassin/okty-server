<?php

declare(strict_types=1);

namespace App\Entity;

class Environment
{
    private $key;
    private $value;

    public function __construct(string $key, string $value)
    {
        $this->key = $key;
        $this->value = $value;
    }
}
