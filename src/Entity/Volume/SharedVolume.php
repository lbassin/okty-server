<?php

declare(strict_types=1);

namespace App\Entity\Volume;

use App\Entity\Volume;

class SharedVolume extends Volume
{
    private $host;
    private $container;

    public function __construct(string $host, string $container)
    {
        $this->host = $host;
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
