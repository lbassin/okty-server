<?php declare(strict_types=1);

namespace App\GraphQL\Resolver\Container;

use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

class Validators implements ResolverInterface
{
    public function __invoke(array $container): array
    {
        if (empty($container['validators'])) {
            return [];
        }

        $validators = [];
        foreach ($container['validators'] as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }

            $validators[] = [
                'name' => $key,
                'constraint' => $value
            ];
        }

        return $validators;
    }
}
