<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Port;

class PortFactory
{
    public function createAll(array $ports): array
    {
        $output = [];
        foreach ($ports as $port) {
            $output[] = new Port((int) $port['host'], (int) $port['container'], $port['local_only'] ?? true);
        }

        return $output;
    }
}
