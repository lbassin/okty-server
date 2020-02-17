<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Volume\DockerVolume;
use App\Entity\Volume\SharedVolume;
use LogicException;

class VolumeFactory
{
    public function createAllFromRequest(array $request): array
    {
        $volumes = $request['args']['volumes'] ?? [];

        return array_map(function ($volume) {
            if ($volume['type'] === 'shared') {
                return new SharedVolume($volume['host'], $volume['container']);
            }

            if ($volume['type'] === 'docker') {
                return new DockerVolume($volume['name'], $volume['container']);
            }

            throw new LogicException(sprintf('"%s" is not a valid volume type', $volume['type']));
        }, $volumes);
    }
}
