<?php declare(strict_types=1);

namespace App\Factory\Docker\Resolver;

use App\ValueObject\Service\Args;
use App\ValueObject\Service\Environment;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class EnvironmentsResolver
{
    public function resolve(Args $args): array
    {
        $output = [];

        /** @var Environment $env */
        foreach ($args->getEnvironments() as $env) {
            $output[] = sprintf('%s=%s', $env->getKey(), $env->getValue());
        }

        return $output;
    }
}
