<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Factory\Docker\ComposeFactory;
use App\ValueObject\Service\Args;
use App\ValueObject\Json;
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

    public function __construct(ComposeFactory $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @Route("preview", methods={"POST"})
     */
    public function single(Request $request): Response
    {
        $args = new Json($request->getContent());
        $containerArgs = new Args($args->getValue());

        $compose = $this->builder->build([$containerArgs]);

        return new JsonResponse(['content' => $compose->toArray()]);
    }

    /**
     * @Route("preview/full", methods={"POST"})
     */
    public function full(Request $request): Response
    {
        $args = new Json($request->getContent());

        $containers = [];
        foreach ($args->getValue() as $config) {
            $containers[] = new Args($config);
        }

        $compose = $this->builder->build($containers);

        return new JsonResponse(['content' => $compose->toArray()]);
    }
}
