<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Image;
use App\Entity\Image\Repository;

class ImageFactory
{
    public function createAllFromRequest(array $request): Image
    {
        $name = $request['template']; // Todo replace with getDockerImageFromTemplate($template)
        $tag = $request['args']['version'] ?? 'latest';

        // Todo check from template if a dockerfile is provided and use Image\Build

        return new Repository($name, $tag);
    }
}
