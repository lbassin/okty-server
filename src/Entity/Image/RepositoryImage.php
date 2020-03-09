<?php

declare(strict_types=1);

namespace App\Entity\Image;

use App\Entity\Image;

class RepositoryImage extends Image
{
    private $name;
    private $tag;

    public function __construct(string $name, ?string $tag)
    {
        $this->name = $name;
        $this->tag = $tag ?: 'latest';
    }

    public function setTag(string $tag): void
    {
        $this->tag = $tag;
    }

    public function __toString(): string
    {
        return sprintf('%s:%s', $this->name, $this->tag);
    }
}
