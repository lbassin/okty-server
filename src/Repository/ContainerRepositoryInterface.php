<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Container;
use App\ValueObject\Container\Manifest;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
interface ContainerRepositoryInterface
{
    public function findAll(): array;

    public function findOneById(string $id): Container;

    public function findManifestByContainerId(string $id): Manifest;
}
