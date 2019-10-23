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
    public function findAll(string $language): array;

    /** @throws EntityNotFoundException */
    public function findById(string $id): Chapter;

    public function createFromValueObject(\App\ValueObject\Learning\Github\Chapter $chapter, string $language): Chapter;

    public function save(Chapter $chapter): void;

    public function clear(): void;
}
