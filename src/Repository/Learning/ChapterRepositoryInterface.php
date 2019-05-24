<?php

declare(strict_types=1);

namespace App\Repository\Learning;

use App\Entity\Learning\Chapter;
use Doctrine\ORM\EntityNotFoundException;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
interface ChapterRepositoryInterface
{
    public function findAll(): array;

    /** @throws EntityNotFoundException */
    public function findById(string $id): Chapter;
}
