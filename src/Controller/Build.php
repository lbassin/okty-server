<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class Build
{
    /**
     * @Route(path="/build", methods={"GET"})
     */
    public function __invoke(): Response
    {
        return new Response('test');
    }
}
