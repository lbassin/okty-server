<?php declare(strict_types=1);

namespace App\GraphQL\Resolver\Container;

use App\Provider\Container;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

class Containers implements ResolverInterface
{
    private $containerProvider;

    public function __construct(Container $containerProvider)
    {
        $this->containerProvider = $containerProvider;
    }

    public function __invoke(): array
    {
        return $this->containerProvider->getAll();
    }
}
