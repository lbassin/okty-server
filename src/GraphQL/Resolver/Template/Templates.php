<?php declare(strict_types=1);

namespace App\GraphQL\Resolver\Template;

use App\Provider\Config\ConfigProvider;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

class Templates implements ResolverInterface
{
    private $configProvider;

    public function __construct(ConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    public function __invoke(): array
    {
        return $this->configProvider->getAllTemplates();
    }
}
