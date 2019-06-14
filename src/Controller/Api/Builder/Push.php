<?php

declare(strict_types=1);

namespace App\Controller\Api\Builder;

use App\Service\Github;
use App\ValueObject\File;
use App\ValueObject\Github\Author;
use App\ValueObject\Github\Target;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        $files = [
            new File('test1.txt', '# Salut 1'),
            new File('test2.txt', '# Salut 2'),
        ];

        $imageName = 'tomcat';

        $author = new Author('Laurent', 'laurent@email.fr');
        $target = new Target(
            "container-$imageName",
            "Add container $imageName",
            "containers/$imageName"
        );

        $this->github->upload($files, $author, $target);

        $title = "Add $imageName container";
        $message = 'Thank you';

        $url = $this->github->requestMerge($target, $title, $message);

        return new JsonResponse(['url' => $url], Response::HTTP_OK);
    }
}
