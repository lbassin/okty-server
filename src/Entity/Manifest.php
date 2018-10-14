<?php declare(strict_types=1);

namespace App\Entity;

class Manifest
{
    /**
     * @var array
     */
    public $files;
    /**
     * @var array
     */
    public $config;

    public function getFiles(): array
    {
        return $this->files;
    }

    public function setFiles(array $files): Manifest
    {
        $this->files = $files;
        return $this;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function setConfig(array $config): Manifest
    {
        $this->config = $config;
        return $this;
    }
}
