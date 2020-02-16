<?php declare(strict_types=1);

namespace App\Factory\Docker\Resolver;

use App\ValueObject\Service\Args;
use App\ValueObject\Service\Volume;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class VolumesResolver
{
    public function resolve(Args $args): array
    {
        $output = [];

        /** @var Volume $volume */
        foreach ($args->getVolumes() as $volume) {
            $output[] = sprintf('%s:%s', $volume->getHost(), $volume->getContainer());
        }

        return $output;
    }
}
