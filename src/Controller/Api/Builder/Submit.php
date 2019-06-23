<?php

declare(strict_types=1);

namespace App\Controller\Api\Builder;

use App\Service\Builder\PullRequest;
use App\Service\Github;
use App\ValueObject\File;
use App\ValueObject\Github\Author;
use App\ValueObject\Github\Target;
use App\ValueObject\Json;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Submit
{
    private $github;
    private $pullRequest;

    public function __construct(Github $github, PullRequest $pullRequest)
    {
        $this->github = $github;
        $this->pullRequest = $pullRequest;
    }

    /**
     * @Route("builder/submit", methods={"POST"})
     */
    public function handle(Request $request): Response
    {
        $data = new Json($request->getContent());

        $container = $data->getData('container');
        $form = $data->getData('form');

        $files = [
            new File('manifest.yml', $this->pullRequest->requestToManifestContent($container)),
            new File('form.yml', $this->pullRequest->requestToFormContent($form)),
        ];

        $author = new Author('Anonymous', 'anonymous@email.fr'); // TODO Change with real user

        $target = new Target(
            "container-{$container['image']}",
            "Add container {$container['image']}",
            'containers/'.str_replace('/', '-', $container['image'])
        );

        $this->github->upload($files, $author, $target);

        $title = $this->pullRequest->getTitle($container);
        $message = $this->pullRequest->getMessage($container);

        $url = $this->github->requestMerge($target, $title, $message);

        return new JsonResponse(['url' => $url], Response::HTTP_OK);
    }
}
