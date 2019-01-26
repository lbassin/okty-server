<?php declare(strict_types=1);

namespace App\Builder\Resolver;

use App\Builder\ValueObject\ContainerArgs;
use App\Builder\ValueObject\Project\Environment;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class EnvironmentsResolver
{
    public function resolve(ContainerArgs $args): array
    {
        $output = [];

        /** @var Environment $env */
        foreach ($args->getEnvironments() as $env) {
            $output[] = sprintf('%s=%s', $env->getKey(), $env->getValue());
        }

        return $output;
    }
}
