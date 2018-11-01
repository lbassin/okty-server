<?php declare(strict_types=1);

namespace App\Controller\Container;

use Symfony\Component\HttpFoundation\Response;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Build
{
    public function handle(): Response
    {
        return new Response('Container Build');
    }
}
