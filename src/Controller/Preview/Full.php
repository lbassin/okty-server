<?php declare(strict_types=1);

namespace App\Controller\Preview;

use Symfony\Component\HttpFoundation\Response;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Full
{
    public function handle(): Response
    {
        return new Response('');
    }
}
