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
        array_walk($services, function ($service) {
            if (!$service instanceof Service){
                throw new \InvalidArgumentException('An array of service is expected');
            }
        });

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
