<?php

declare(strict_types=1);

namespace App\Entity\Volume;

use App\Entity\Volume;

class DockerVolume extends Volume
{
    private $name;
    private $container;

    public function __construct(string $name, string $container)
    {
        $this->name = $name;
        $this->container = $container;
    }
}
