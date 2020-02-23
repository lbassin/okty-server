<?php

declare(strict_types=1);

namespace App\ValueObject;

class Id
{
    private $name;

    public function __construct(string $name)
    {
        if (empty($name)) {
            throw new \LogicException("Id cannot be empty");
        }

        if (!preg_match('/^([a-zA-Z0-9]-?)*[a-zA-Z0-9]+$/', $name)) {
            throw new \LogicException("Id doesn't match the required format");
        }

        $this->name = $name;
    }

    public function __toString()
    {
        return $this->name;
    }
}
