<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Image;
use App\Entity\Image\RepositoryImage;

class ImageFactory
{
    public function create(string $template, string $tag): Image
    {
        // Todo add getDockerImageFromTemplate($template)
        // Todo check from template if a dockerfile is provided and use Image\Build

        return new RepositoryImage($template, $tag);
    }
}
