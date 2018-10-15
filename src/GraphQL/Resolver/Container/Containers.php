<?php declare(strict_types=1);

namespace App\GraphQL\Resolver\Container;

use App\Provider\Config;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

class Containers implements ResolverInterface
{
    private $configProvider;

    public function __construct(Config $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    public function __invoke(): array
    {
        return $this->configProvider->getAllContainers();
    }
}
