<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Builder\DockerComposerBuilder;
use App\Builder\ValueObject\ContainerArgs;
use App\ValueObject\Json;
use App\Builder\ValueObject\Project\DockerCompose;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Preview
{
    private $builder;

    public function __construct(DockerComposerBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @Route("preview", methods={"POST"})
     */
    public function single(Request $request): Response
    {
        $project = new DockerCompose();

        $args = new Json($request->getContent());
        $containerArgs = new ContainerArgs($args->getValue());

        try {
            $this->builder->build($project, $containerArgs);
        } catch (\LogicException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['content' => $project->toArray()]);
    }

    /**
     * @Route("preview/full", methods={"POST"})
     */
    public function full(Request $request): Response
    {
        $project = new DockerCompose();
        $args = new Json($request->getContent());

        try {
            foreach ($args->getValue() as $config) {
                $containerArgs = new ContainerArgs($config);

                $this->builder->build($project, $containerArgs);
            }
        } catch (\LogicException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['content' => $project->toArray()]);
    }
}
