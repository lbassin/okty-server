<?php declare(strict_types=1);

namespace App\Controller\Container\Form;

use App\Provider\ContainerProvider;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Show
{
    private $containerProvider;

    public function __construct(ContainerProvider $containerProvider)
    {
        $this->containerProvider = $containerProvider;
    }

    public function handle(Request $request): Response
    {
        $id = $request->attributes->get('id');

        try {
            $container = $this->containerProvider->getFormConfig($id);
        } catch (FileNotFoundException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($container);
    }
}
