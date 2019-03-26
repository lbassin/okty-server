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
            $value = $option->getValue();

            if ($option->getKey() === 'command') {
                $value = $this->formatCommandValue($value);
                $value = count($value) === 1 ? reset($value) : $value;
            }

            if (is_string($value)) {
                $value = trim($value);
            }

            $output[$option->getKey()] = $value;
        }

        return $output;
    }

    private function formatCommandValue(string $value): array
    {
        if (strpos($value, '&&') === false) {
            return [$value];
        }

        return ['/bin/sh', '-c', $value];
    }
}
