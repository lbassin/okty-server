<?php

declare(strict_types=1);

namespace App\Repository\Learning;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
interface ChapterRepositoryInterface
{
    public function findAll(): array;
}
