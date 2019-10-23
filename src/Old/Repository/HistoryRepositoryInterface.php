<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\History;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
interface HistoryRepositoryInterface
{
    public function findAllByUserId(string $userId);

    public function save(History $history);
}
