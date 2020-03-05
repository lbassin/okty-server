<?php

declare(strict_types=1);

namespace App\ValueObject;

use App\Exception\ValueObject\InvalidIdException;

class Id
{
    private $name;

    public function __construct(string $name)
    {
        if (empty($name)) {
            throw new InvalidIdException($name);
        }

        if (!preg_match('/^([a-zA-Z0-9]-?)*[a-zA-Z0-9]+$/', $name)) {
            throw new InvalidIdException($name);
        }

        $this->name = $name;
    }

    public function __toString()
    {
        return $this->name;
    }
}
