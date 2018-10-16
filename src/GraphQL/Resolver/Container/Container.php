<?php declare(strict_types=1);

namespace App\GraphQL\Resolver\Container;

use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

class Container implements ResolverInterface
{
    private $containerProvider;

    public function __construct(\App\Provider\Container $containerProvider)
    {
        $this->containerProvider = $containerProvider;
    }

    public function __invoke(string $id): array
    {
        return $this->containerProvider->getFormConfig($id . '.yml');
    }

}