<?php

namespace App\Controller;

use App\Builder\ContainerBuilder;
use App\Helper\ZipHelper;
use App\Provider\Cloud;
use App\Provider\ContainerProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @codeCoverageIgnore
 */
class IndexController extends AbstractController
{
    private $containerBuilder;
    private $zipHelper;
    private $cloud;

    /**
     * IndexController constructor.
     * @param ContainerProvider $provider
     */
    public function __construct(
        ContainerBuilder $containerBuilder,
        ZipHelper $zipHelper,
        Cloud $cloud
    )
    {
        $this->containerBuilder = $containerBuilder;
        $this->zipHelper = $zipHelper;
        $this->cloud = $cloud;
    }

    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return new Response('Okty API', Response::HTTP_OK);
    }

    /**
     * @Route("/dev", name="dev")
     */
    public function dev()
    {

    }
}
