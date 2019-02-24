<?php declare(strict_types=1);

namespace App\Factory\Docker\Resolver;

use App\ValueObject\Service\Args;
use App\ValueObject\Service\Option;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class OptionsResolver
{
    public function resolve(Args $args): array
    {
        $output = [];

        /** @var Option $option */
        foreach ($args->getComposeOptions() as $option) {
            $output[$option->getKey()] = $option->getValue();
        }

        return $output;
    }
}
