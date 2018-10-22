<?php

namespace App\Controller;

use App\Builder\ContainerBuilder;
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

    /**
     * IndexController constructor.
     * @param ContainerProvider $provider
     */
    public function __construct(ContainerBuilder $containerBuilder)
    {
        $this->containerBuilder = $containerBuilder;
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
        $name = 'nginx';
        $args = ['id' => 'nginx'];

        $files = $this->containerBuilder->build($name, $args);

        foreach ($files as $file) {
            echo $file['name'] . PHP_EOL;
            echo $file['content'] . PHP_EOL;
            echo "<hr>";
        }

        return new Response('', Response::HTTP_OK);
    }
}
