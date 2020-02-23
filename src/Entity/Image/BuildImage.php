<?php

declare(strict_types=1);

namespace App\Entity\Image;

use App\Entity\Image;

class BuildImage extends Image
{

    public function __toString(): string
    {
        return '';
    }
}
