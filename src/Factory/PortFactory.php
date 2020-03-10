<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Port;
use App\Exception\HostPortAlreadyMappedException;

class PortFactory
{
    public function createAll(array $ports): array
    {
        $usedPortOnHost = [];

        $output = [];
        foreach ($ports as $port) {
            $output[] = new Port((int) $port['host'], (int) $port['container'], $port['local_only'] ?? true);

            if (key_exists((string) $port['host'], $usedPortOnHost)) {
                throw new HostPortAlreadyMappedException((string) $port['host']);
            }
            $usedPortOnHost[$port['host']] = true;
        }

        return $output;
    }
}
