<?php

declare(strict_types=1);

namespace App\Builder\ValueObject\Project;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Service
{
    private $id;
    private $image;
    private $build;
    private $options;
    private $ports;
    private $volumes;
    private $environments;

    public function __construct(string $id, array $image, array $options, array $ports, array $volumes, array $environments)
    {
        $this->setId($id);
        $this->setImage($image);
        $this->setOptions($options);
        $this->setPorts($ports);
        $this->setVolumes($volumes);
        $this->setEnvironments($environments);
    }

    private function setId(string $id): void
    {
        $this->id = $id;
    }

    private function setImage(array $data): void
    {
        $this->image = $data['image'] ?? '';
        $this->build = $data['build'] ?? '';

        if (empty($this->image) && empty($this->build)) {
            throw new \LogicException("At least one of these two options is required (Image/Build");
        }
    }

    private function setOptions(array $options): void
    {
        $this->options = $options;
    }

    private function setPorts(array $ports): void
    {
        $this->ports = $ports;
    }

    private function setVolumes(array $volumes): void
    {
        $this->volumes = $volumes;
    }

    private function setEnvironments(array $environments): void
    {
        $this->environments = $environments;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function toArray(): array
    {
        $output = [];
        $output[$this->image ? 'image' : 'build'] = $this->image ? $this->image : $this->build;
        $output = array_merge($output, $this->options);

        $output['ports'] = $this->ports;
        $output['volumes'] = $this->volumes;
        $output['environment'] = $this->environments;

        return array_filter($output);
    }
}
