<?php declare(strict_types=1);

namespace App\GraphQL\Resolver\Template;

use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

class Template implements ResolverInterface
{
    private $templateProvider;

    public function __construct(\App\Provider\Template $templateProvider)
    {
        $this->templateProvider = $templateProvider;
    }

    public function __invoke(string $id): array
    {
        return $this->templateProvider->getOne($id . '.yml');
    }
}
