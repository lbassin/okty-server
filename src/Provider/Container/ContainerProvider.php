<?php declare(strict_types=1);

namespace App\Provider\Container;

use App\Entity\Manifest;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
interface ContainerProvider
{
    public function getManifest($container): Manifest;

    public function getAllFilenames($container): array;

    public function getAllFileConfig($container): array;

    public function getFileConfig($container, $file): array;
}
