<?php declare(strict_types=1);

namespace App\GraphQL\Resolver\Template;

use App\Provider\TemplateProvider;
use GraphQL\Error\UserError;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class All implements ResolverInterface
{
    private $templateProvider;

    public function __construct(TemplateProvider $templateProvider)
    {
        $this->templateProvider = $templateProvider;
    }

    public function __invoke(): array
    {
        try {
            return $this->templateProvider->getAll();
        } catch (FileNotFoundException $ex) {
            throw new UserError('Templates list is not available');
        }
    }
}
