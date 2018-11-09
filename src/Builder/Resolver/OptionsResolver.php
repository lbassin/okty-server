<?php declare(strict_types=1);

namespace App\Builder\Resolver;

class OptionsResolver
{
    private $ignoredKeys = [
        'id',
        'version',
        'volumes',
        'ports',
        'files',
        'environments',
        'image',
        'build'
    ];

    public function resolve(array $args): array
    {
        $output = [];

        foreach ($args as $key => $value) {
            if (in_array($key, $this->ignoredKeys)) {
                continue;
            }

            $output[$key] = $value;
        }

        return $output;
    }
}
