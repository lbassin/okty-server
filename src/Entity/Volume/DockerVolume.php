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

    public function getName(): string
    {
        return $this->name;
    }

    public function getTarget(): string
    {
        return sprintf('/%s', ltrim($this->container, "/"));
    }
}
