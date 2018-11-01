<?php declare(strict_types=1);

namespace App\Controller\Template;

use Symfony\Component\HttpFoundation\Response;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Index
{
    public function handle(): Response
    {
        return new Response('Template Index');
    }
}
