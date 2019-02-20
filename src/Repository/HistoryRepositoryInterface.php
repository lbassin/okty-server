<?php

declare(strict_types=1);

namespace App\Repository;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
interface HistoryRepositoryInterface
{
    public function findAllByUserId(string $userId);
}
