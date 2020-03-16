<?php

declare(strict_types=1);

namespace App\Entity\Volume;

use App\Entity\Volume;
use App\Exception\ValueObject\EmptyVolumePathInContainerException;

class SharedVolume extends Volume
{
    private $host;
    private $container;

    public function __construct(string $host, string $container)
    {
        if (empty($container)) {
            throw new EmptyVolumePathInContainerException($container);
        }

        $this->host = './'.ltrim($host, './');
        $this->container = $container;
    }

    public function getSource(): string
    {
        return sprintf('./%s', ltrim($this->host, "./"));
    }

    public function getTarget(): string
    {
        return sprintf('/%s', ltrim($this->container, "/"));
    }
}
