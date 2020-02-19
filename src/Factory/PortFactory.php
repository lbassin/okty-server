<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Port;

class PortFactory
{
    public function createAllFromRequest(array $request): array
    {
        $ports = $request['args']['ports'] ?? [];

        $output = [];
        foreach ($ports as $port) {
            $output[] = new Port((int) $port['host'], (int) $port['container'], $port['local_only'] ?? true);
        }

        return $output;
    }
}
