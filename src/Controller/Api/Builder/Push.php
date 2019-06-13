<?php

declare(strict_types=1);

namespace App\Controller\Api\Builder;

use App\Service\Github;
use App\ValueObject\File;
use App\ValueObject\Github\Author;
use App\ValueObject\Github\Target;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Push
{
    private $github;

    public function __construct(Github $github)
    {
        $this->github = $github;
    }

    /**
     * @Route("builder/push", methods={"GET"})
     */
    public function handle(): Response
    {
        $file = new File('test.txt', '# Salut');
        $author = new Author('Laurent', 'laurentbassin91@gmail.com');
        $target = new Target('master', 'Add new file');

        $this->github->upload($file, $author, $target);

        return new Response('ok');
    }
}
