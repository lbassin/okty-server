<?php

declare(strict_types=1);

namespace App\Entity;

use App\ValueObject\Id;

class Container
{
    /** @var Id */
    private $id;
    /** @var Image */
    private $image;
    /** @var Port[] */
    private $ports;
    /** @var Environment[] */
    private $environments;
    /** @var Volume[] */
    private $volumes;
    /** @var Option[] $options */
    private $options;

    public function __construct(Id $id, Image $image, array $ports, array $environments, array $volumes, array $options)
    {
        $this->id = $id;
        $this->image = $image;
        $this->ports = $ports;
        $this->environments = $environments;
        $this->volumes = $volumes;
        $this->options = $options;
    }

    public function getId(): Id
    {
        return $this->id;
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

    public function getCommand(): string
    {
        return $this->getOptionValue('command');
    }

    public function getWorkingDir(): string
    {
        return $this->getOptionValue('working_dir');
    }

    private function getOptionValue(string $name): string
    {
        foreach ($this->options as $option) {
            if ($option->getName() === $name) {
                return $option->getValue();
            }
        }

        return '';
    }
}
