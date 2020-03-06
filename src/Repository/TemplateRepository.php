<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Image\BuildImage;
use App\Entity\Image\RepositoryImage;
use App\Entity\Template;
use App\Exception\TemplateNotFoundException;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;

class TemplateRepository implements TemplateRepositoryInterface
{
    private $configPath;
    private $finder;

    public function __construct(Finder $finder, string $configPath)
    {
        $this->finder = $finder;
        $this->configPath = $configPath;
    }

    public function getList(): array
    {
        return [];
    }

    public function getOne(string $name): Template
    {
        $path = sprintf('%s/var/config/containers/%s', rtrim($this->configPath, '/'), $name);

        try {
            $files = $this->finder->in($path)->files()->name('manifest.yml');
        } catch (DirectoryNotFoundException $exception) {
            throw new TemplateNotFoundException($name, $exception);
        }

        $manifest = null;

        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $manifest = Yaml::parse($file->getContents());
        }

        if (!$manifest) {
            throw new TemplateNotFoundException($name);
        }

        $image = $this->getImageFromManifest($manifest);

        return new Template($image);
    }

    private function getImageFromManifest($manifest)
    {
        $image = null;
        $dockerConfig = $manifest['docker'];
        
        if (!empty($dockerConfig['build'])) {
            $image = new BuildImage($dockerConfig['build'] ?? '');
        }

        if (!empty($dockerConfig['image'])) {
            $image = new RepositoryImage($dockerConfig['image'], $dockerConfig['tag'] ?? null);
        }

        if (!$image) {
            throw new \RuntimeException('Cannot build image for template');
        }

        return $image;
    }
}

/**
 *
 * docker:
 * image: 'adminer'
 * tag: '4.7'
 *
 * ==============
 *
 * docker:
 * build: 'docker/php'
 * files:
 * - Dockerfile
 * config:
 * Dockerfile:
 * output: docker/php
 * args:
 * php_extensions:
 * default: ''
 */