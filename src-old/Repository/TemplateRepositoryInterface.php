<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Template;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
interface TemplateRepositoryInterface
{
    public function findAll(): array;

    public function findOneById(string $id): Template;
}
