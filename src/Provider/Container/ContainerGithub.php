<?php declare(strict_types=1);

namespace App\Provider\Container;

use App\Entity\Manifest;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class ContainerGithub implements ContainerProvider
{

    public function getManifest($container): Manifest
    {
        return new Manifest();
    }

    public function getAllFilenames($container): array
    {
        return [];
    }

    public function getAllFileConfig($container): array
    {
        return [];
    }

    public function getFileConfig($container, $file): array
    {
        return [];
    }
}
