<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

final class Home
{
    public function handle(): Response
    {
        return new Response('Okty', Response::HTTP_OK);
    }
}
