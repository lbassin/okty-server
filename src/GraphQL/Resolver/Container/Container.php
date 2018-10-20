<?php declare(strict_types=1);

namespace App\GraphQL\Resolver\Container;

use GraphQL\Error\UserError;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class Container implements ResolverInterface
{
    private $containerProvider;

    public function __construct(\App\Provider\ContainerProvider $containerProvider)
    {
        $this->containerProvider = $containerProvider;
    }

    public function __invoke(string $id): array
    {
        try{
            return $this->containerProvider->getFormConfig($id . '.yml');
        } catch (FileNotFoundException $ex){
            throw new UserError('Container not found');
        }
    }

}