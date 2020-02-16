<?php

declare(strict_types=1);

namespace App\Repository\Learning;

use App\Entity\Learning\Action;
use Doctrine\ORM\EntityNotFoundException;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
interface ActionRepositoryInterface
{
    /** @throws EntityNotFoundException */
    public function findById(string $id): Action;
}
