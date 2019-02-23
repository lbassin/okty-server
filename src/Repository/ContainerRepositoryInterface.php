<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Container;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
interface ContainerRepositoryInterface
{
    public function findAll(): array;

    public function findOneById(string $id): Container;
}
