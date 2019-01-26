<?php declare(strict_types=1);

namespace App\Controller\Api\Container;

use App\Provider\ContainerProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

    /**
     * @Route("containers", methods={"GET"})
     */
    public function handle(): Response
    {
        $containers = $this->containerProvider->getList();

        return new JsonResponse($containers);
    }
}
