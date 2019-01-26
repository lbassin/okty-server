<?php declare(strict_types=1);

namespace App\Builder\Resolver;

use App\Builder\ValueObject\ContainerArgs;
use App\Builder\ValueObject\Project\Port;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class PortsResolver
{
    public function resolve(ContainerArgs $args): array
    {
        $output = [];

        /** @var Port $port */
        foreach ($args->getPorts() as $port) {
            $output[] = sprintf('%d:%d', $port->getHost(), $port->getContainer());
        }
        return $output;
    }
}
