<?php

declare(strict_types=1);

namespace App\Builder\ValueObject\Container;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Manifest
{
    private $image;
    private $build;
    private $tag;

    private $files;

    public function __construct(array $config)
    {
        $this->image = $config['docker']['image'] ?? '';
        $this->build = $config['docker']['build'] ?? '';
        $this->tag = $config['docker']['tag'] ?? '';

        $this->files = [];
        foreach ($config['files'] ?? [] as $filename) {
            if (!$config['config'][$filename]) {
                continue;
            }

            $this->files[$filename] = new FileConfig($config['config'][$filename]);
        }
    }

    public function hasBuild(): bool
    {
        return (bool)$this->build;
    }

    public function getBuild(): string
    {
        return $this->build;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function getTag(): string
    {
        return $this->tag ?? 'latest';
    }

    public function hasFiles(): bool
    {
        return !empty($this->files);
    }

    public function getFiles(): array
    {
        return $this->files;
    }
}
