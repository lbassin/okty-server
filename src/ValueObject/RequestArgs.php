<?php

declare(strict_types=1);

namespace App\ValueObject;

class RequestArgs
{
    private $id;
    private $version;
    private $ports;
    private $environments;
    private $volumes;
    private $options;

    public function __construct(array $args)
    {
        $this->id = $args['id'];
        $this->version = $args['version'] ?? 'latest';
        $this->ports = $args['ports'] ?? [];
        $this->environments = $args['environments'] ?? [];
        $this->volumes = $args['volumes'] ?? [];
        $this->options = $args['options'] ?? [];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function getPorts(): array
    {
        return $this->ports;
    }

    public function getEnvironments(): array
    {
        return $this->environments;
    }

    public function getVolumes(): array
    {
        return $this->volumes;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
