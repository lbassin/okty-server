<?php declare(strict_types=1);

namespace App\GraphQL\Resolver\Template;

use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

class Config implements ResolverInterface
{
    public function __invoke(array $container): array
    {
        $validators = [];
        foreach ($container['config'] as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }

            $validators[] = [
                'label' => $key,
                'value' => $value
            ];
        }

        return $validators;
    }
}
