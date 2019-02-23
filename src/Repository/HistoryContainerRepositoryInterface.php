<?php

declare(strict_types=1);

namespace App\Repository;

use App\Builder\ValueObject\ContainerArgs;
use App\Entity\History;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
interface HistoryContainerRepositoryInterface
{
    public function createFromArgs(History $history, ContainerArgs $args);
}
