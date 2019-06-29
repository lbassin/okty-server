<?php

declare(strict_types=1);

namespace App\Controller\Api\Builder;

use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use App\Service\Builder\PullRequest;
use App\Service\Github;
use App\ValueObject\File;
use App\ValueObject\Github\Author;
use App\ValueObject\Github\Target;
use App\ValueObject\Json;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
    private $userRepository;

    public function __construct(Github $github, PullRequest $pullRequest, UserRepositoryInterface $userRepository)
    {
        $this->github = $github;
        $this->pullRequest = $pullRequest;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("builder/submit", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function handle(Request $request): Response
    {
        $data = new Json($request->getContent());

        $container = $data->getData('container');
        $form = $data->getData('form');
        $userData = $data->getData('user');

        $files = [
            new File('manifest.yml', $this->pullRequest->requestToManifestContent($container)),
            new File('form.yml', $this->pullRequest->requestToFormContent($form)),
        ];

        $user = $this->userRepository->findById($userData['id']);
        if (!$user) {
            $user = new User(0, 'Anonymous', 'anonymous@okty.io', null, null, 'anonymous');
        }

        $author = new Author($user->getLogin(), $user->getEmail());

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
