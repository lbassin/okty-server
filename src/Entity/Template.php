<?php

declare(strict_types=1);

namespace App\Entity;

class Template
{
    /** @var Image */
    private $image;

    public function __construct(Image $image)
    {
        $this->image = $image;
    }

    public function getImage(): Image
    {
        return $this->image;
    }
}
