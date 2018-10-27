<?php declare(strict_types=1);

namespace App\GraphQL\Resolver\Container;

use App\Provider\ContainerProvider;
use GraphQL\Error\UserError;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class Forms implements ResolverInterface
{
    private $containerProvider;

    public function __construct(ContainerProvider $containerProvider)
    {
        $this->containerProvider = $containerProvider;
    }

    public function __invoke(): array
    {
        try {
            return $this->containerProvider->getAll();
        } catch (FileNotFoundException $ex){
            throw new UserError('Containers list is not available');
        }
    }
}
