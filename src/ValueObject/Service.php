<?php

declare(strict_types=1);

namespace App\ValueObject;

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

    public function __construct(
        string $id,
        array $image,
        array $options,
        array $ports,
        array $volumes,
        array $environments
    ) {
        $this->id = $id;

        $this->image = $image['image'] ?? '';
        $this->build = $image['build'] ?? '';

        if (empty($this->image) && empty($this->build)) {
            throw new \LogicException("At least one of these two options is required (Image/Build");
        }

        $this->options = $options;
        $this->ports = $ports;
        $this->volumes = $volumes;
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
