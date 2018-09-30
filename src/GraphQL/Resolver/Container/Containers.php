<?php declare(strict_types=1);

namespace App\GraphQL\Resolver\Container;

use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

class Containers implements ResolverInterface
{
    public function __invoke(): array
    {
        return [];
    }
}
