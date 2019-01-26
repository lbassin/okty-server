<?php declare(strict_types=1);

namespace App\Builder\Resolver;

use App\Builder\ValueObject\ContainerArgs;
use App\Builder\ValueObject\Project\Option;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class OptionsResolver
{
    public function resolve(ContainerArgs $args): array
    {
        $output = [];

        /** @var Option $option */
        foreach ($args->getComposeOptions() as $option) {
            $output[$option->getKey()] = $option->getValue();
        }

        return $output;
    }
}
