<?php

namespace App\Controller;

use App\Builder\ProjectBuilder;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Home
{
    private $projectBuilder;

    public function __construct(ProjectBuilder $projectBuilder)
    {
        $this->projectBuilder = $projectBuilder;
    }

    public function handle(): Response
    {
        return new Response('', Response::HTTP_FORBIDDEN);
    }
}
