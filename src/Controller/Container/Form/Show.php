<?php declare(strict_types=1);

namespace App\Controller\Container\Form;

use Symfony\Component\HttpFoundation\Response;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Show
{
    public function handle(): Response
    {
        return new Response('Container Form Show');
    }
}
