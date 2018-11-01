<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Home
{
    public function handle(): Response
    {
        return new Response('', Response::HTTP_FORBIDDEN);
    }
}
