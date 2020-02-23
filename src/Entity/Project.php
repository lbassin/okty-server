<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Volume\DockerVolume;

class Project
{
    public const DEFAULT_VERSION = '3.3';

    /** @var string $version */
    private $version;
    /** @var Container[] $containers */
    private $containers = [];
    /** @var DockerVolume[] $volumes */
    private $volumes = [];

    public function __construct(array $containers = [], string $version = self::DEFAULT_VERSION)
    {
        $this->version = $version;

        foreach ($containers as $container) {
            $this->addContainer($container);
        }
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getContainers(): array
    {
        return $this->containers;
    }

    public function getVolumes(): array
    {
        return $this->volumes;
    }

    public function addContainer(Container $container): void
    {
        foreach ($container->getVolumes() as $volume) {
            if ($volume instanceof DockerVolume) {
                $this->addVolume($volume);
            }
        }

        $this->containers[] = $container;

    }

    public function addVolume(DockerVolume $newVolume): void
    {
        foreach ($this->volumes as $volume) {
            if ($newVolume->getName() === $volume->getName()) {
                return;
            }
        }

        $this->volumes[] = $newVolume;
    }
}
