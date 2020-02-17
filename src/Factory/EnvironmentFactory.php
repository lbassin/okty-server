<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Environment;

class EnvironmentFactory
{
    public function createAllFromRequest(array $request): array
    {
        $environments = $request['args']['environments'] ?? [];

        $output = [];

        foreach ($environments as $env) {
            $output[] = new Environment($env['key'], $env['value']);
        }

        return $output;
    }
}
