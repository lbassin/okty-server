<?php

declare(strict_types=1);

namespace App\Entity\Image;

use App\Entity\Image;

class BuildImage extends Image
{
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function __toString(): string
    {
        return $this->path;
    }
}
