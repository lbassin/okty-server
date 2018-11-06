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
        $args = [
            ['image' => 'nginx', 'args' => ['id' => 'nginx', 'files' => ['root_folder' => 'public'], 'environments' => ['test=43']]],
            ['image' => 'nginx', 'args' => ['id' => 'coucou', 'files' => ['root_folder' => 'test'], 'environments' => ['test=43']]],
            ['image' => 'composer', 'args' => ['id' => 'composer', 'ports' => ['8080:80']]]
        ];

        $files = $this->projectBuilder->build($args);

        dump($files);

        return new Response('', Response::HTTP_FORBIDDEN);
    }
}
