<?php declare(strict_types=1);

namespace App\Builder\Resolver;

use App\Builder\ValueObject\Compose;

class OptionsResolver
{
    public function resolve(array $args): array
    {
        $output = [];

        foreach ($args as $data) {
            $key = $data['key'] ?? '';
            $value = $data['value'] ?? '';

            $compose = new Compose($key, $value);

            $output[$compose->getKey()] = $compose->getValue();
        }

        return $output;
    }
}
