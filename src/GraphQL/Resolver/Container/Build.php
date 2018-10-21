<?php declare(strict_types=1);

namespace App\GraphQL\Resolver\Container;

use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

class Build implements ResolverInterface
{
    public function __invoke(): string
    {
        return 'yes';
    }
}
