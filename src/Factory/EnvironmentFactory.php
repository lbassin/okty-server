<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Environment;

class EnvironmentFactory
{
    public function createAll(array $environments): array
    {
        $output = [];
        foreach ($environments as $env) {
            $output[] = new Environment((string) $env['key'], (string) $env['value']);
        }

        return $output;
    }
}
