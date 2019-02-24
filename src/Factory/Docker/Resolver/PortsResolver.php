<?php declare(strict_types=1);

namespace App\Factory\Docker\Resolver;

use App\ValueObject\Service\Args;
use App\ValueObject\Service\Port;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class PortsResolver
{
    public function resolve(Args $args): array
    {
        $output = [];

        /** @var Port $port */
        foreach ($args->getPorts() as $port) {
            $output[] = sprintf('%d:%d', $port->getHost(), $port->getContainer());
        }
        return $output;
    }
}
