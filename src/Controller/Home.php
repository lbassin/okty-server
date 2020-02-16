<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class Home
{
    /**
     * @Route(path="/", methods={"GET"})
     */
    public function handle(): Response
    {
        return new Response('Okty', Response::HTTP_OK);
    }
}
