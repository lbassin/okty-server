<?php

declare(strict_types=1);

namespace App\ValueObject;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class DockerCompose
{
    private $version = '3.6';
    private $services = [];

    public function __construct(array $services)
    {
        $this->services = $services;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getServices(): array
    {
        return $this->services;
    }
}
