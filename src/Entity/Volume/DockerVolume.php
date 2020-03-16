<?php

declare(strict_types=1);

namespace App\Entity\Volume;

use App\Entity\Volume;
use App\Exception\ValueObject\EmptyVolumePathInContainerException;
use App\Exception\ValueObject\InvalidVolumeNameFormatException;

class DockerVolume extends Volume
{
    private $name;
    private $container;

    public function __construct(string $name, string $container)
    {
        if (empty($name)) {
            throw new InvalidVolumeNameFormatException($name);
        }

        if (empty($container)) {
            throw new EmptyVolumePathInContainerException($container);
        }

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
