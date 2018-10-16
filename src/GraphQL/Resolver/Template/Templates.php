<?php declare(strict_types=1);

namespace App\GraphQL\Resolver\Template;

use App\Provider\Template;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

class Templates implements ResolverInterface
{
    private $templateProvider;

    public function __construct(Template $templateProvider)
    {
        $this->templateProvider = $templateProvider;
    }

    public function __invoke(): array
    {
        return $this->templateProvider->getAll();
    }
}
