<?php

namespace App\Controller;

use App\Provider\Container;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @codeCoverageIgnore
 */
class IndexController extends AbstractController
{
    private $provider;

    /**
     * IndexController constructor.
     * @param Container $provider
     */
    public function __construct(Container $provider)
    {
        $this->provider = $provider;
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
        $manifest = $this->provider->getManifest('nginx');

        print_r($manifest);

        return new Response('', Response::HTTP_OK);
    }
}
