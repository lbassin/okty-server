<?php

declare(strict_types=1);

namespace App\Entity;

abstract class Image
{
    abstract public function __toString(): string;
}
