<?php declare(strict_types=1);

namespace App\Controller\Preview;

use App\Builder\DockerComposerBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Index
{
    private $builder;

    public function __construct(DockerComposerBuilder $builder)
    {
        $this->builder = $builder;
    }

    public function handle(Request $request): Response
    {
        $config = json_decode($request->getContent(), true);
        if (!$config) {
            return new JsonResponse(['error' => 'JSON Syntax Error'], Response::HTTP_BAD_REQUEST);
        }

        if (empty($config['image']) || !isset($config['args'])) {
            return new JsonResponse(['error' => 'Missing mandatory field(s)'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $output = $this->builder->build($config['image'], $config['args']);
        } catch (\LogicException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['content' => $output]);
    }
}
