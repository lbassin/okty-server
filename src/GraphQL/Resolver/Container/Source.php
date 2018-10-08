<?php declare(strict_types=1);

namespace App\GraphQL\Resolver\Container;

use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

class Source implements ResolverInterface
{
    public function __invoke(array $container): array
    {
        if (empty($container['source'])) {
            return [];
        }

        $sources = [];
        foreach ($container['source'] as $key => $value) {
            $sources[] = [
                'label' => $value,
                'value' => $key
            ];
        }

        return $sources;
    }
}
