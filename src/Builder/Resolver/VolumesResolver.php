<?php declare(strict_types=1);

namespace App\Builder\Resolver;

use App\Builder\ValueObject\ContainerArgs;
use App\Builder\ValueObject\Project\Volume;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class VolumesResolver
{
    public function resolve(ContainerArgs $args): array
    {
        $output = [];

        /** @var Volume $volume */
        foreach ($args->getVolumes() as $volume) {
            $output[] = sprintf('%s:%s', $volume->getHost(), $volume->getContainer());
        }

        return $output;
    }
}
