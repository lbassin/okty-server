<?php

declare(strict_types=1);

namespace App\Controller;

use App\Factory\ContainerFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class Build
{

    private $containerFactory;

    public function __construct(ContainerFactory $containerFactory)
    {
        $this->containerFactory = $containerFactory;
    }

    /**
     * @Route(path="/build", methods={"GET"})
     */
    public function handle(Request $request): Response
    {
        $payload = json_decode($request->getContent(), true);

        $containers = [];
        foreach ($payload as $data) {

            // TODO Assert template is provided

            $containers[] = $this->containerFactory->buildFromRequest($data);
            dd($containers);
        }

        dd($containers);

        return new Response('test');
    }
}
