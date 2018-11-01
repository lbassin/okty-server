<?php declare(strict_types=1);

namespace App\Controller\Container\Form;

use App\Provider\ContainerProvider;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Index
{
    private $containerProvider;

    public function __construct(ContainerProvider $containerProvider)
    {
        $this->containerProvider = $containerProvider;
    }

    public function handle(): Response
    {
        try {
            $containers = $this->containerProvider->getAll();
        } catch (FileNotFoundException $exception) {
            return new JsonResponse(['error' => 'Container form not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($containers);
    }
}
