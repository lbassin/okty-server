<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Volume\DockerVolume;
use App\Entity\Volume\SharedVolume;
use InvalidArgumentException;

class VolumeFactory
{
    public function createAll(array $volumes): array
    {
        return array_map(function ($volume) {
            if ($volume['type'] === 'shared') {
                return new SharedVolume($volume['host'], $volume['container']);
            }

            if ($volume['type'] === 'docker') {
                return new DockerVolume($volume['name'], $volume['container']);
            }

            throw new InvalidArgumentException(sprintf('"%s" is not a valid volume type', $volume['type']));
        }, $volumes);
    }
}
