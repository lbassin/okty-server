<?php declare(strict_types=1);

namespace App\GraphQL\Resolver\Template;

use GraphQL\Error\UserError;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class Template implements ResolverInterface
{
    private $templateProvider;

    public function __construct(\App\Provider\TemplateProvider $templateProvider)
    {
        $this->templateProvider = $templateProvider;
    }

    public function __invoke(string $id): array
    {
        try {
            return $this->templateProvider->getOne($id . '.yml');
        } catch (FileNotFoundException $ex) {
            throw new UserError('Template not found');
        }
    }
}
