<?php declare(strict_types=1);

namespace App\GraphQL\Resolver\Template;

use App\Provider\Config\ConfigProvider;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

class Template implements ResolverInterface
{
    private $configProvider;

    public function __construct(ConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    public function __invoke(string $id): array
    {
        return $this->configProvider->getTemplate($id . '.yml');
    }
}
