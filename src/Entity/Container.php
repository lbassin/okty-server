<?php

declare(strict_types=1);

namespace App\Entity;

class Container
{
    /** @var Image */
    private $image;
    /** @var Port[] */
    private $ports;
    /** @var Environment[] */
    private $environments;
    /** @var Volume[] */
    private $volumes;

    public function __construct(Image $image, array $ports, array $environments, array $volumes)
    {
        $this->image = $image;
        $this->ports = $ports;
        $this->environments = $environments;
        $this->volumes = $volumes;
    }

    public function getImage(): Image
    {
        return $this->image;
    }

    public function getPorts(): array
    {
        return $this->ports;
    }

    public function getVolumes(): array
    {
        return $this->volumes;
    }

    public function getEnvironments(): array
    {
        return $this->environments;
    }
}
