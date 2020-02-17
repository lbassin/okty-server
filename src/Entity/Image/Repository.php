<?php

declare(strict_types=1);

namespace App\Entity\Image;

use App\Entity\Image;

class Repository extends Image
{
    private $name;
    private $tag;

    public function __construct(string $name, ?string $tag = 'latest')
    {
        $this->name = $name;
        $this->tag = $tag ?: 'latest';
    }

    public function __toString(): string
    {
        return sprintf('%s:%s', $this->name, $this->tag);
    }
}
