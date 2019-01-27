<?php

declare(strict_types=1);

namespace App\Controller\Api\User;

use App\Provider\Github;
use App\ValueObject\Json;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Create
{
    private $github;

    public function __construct(Github $github)
    {
        $this->github = $github;
    }

    /**
     * @Route("users", methods={"POST"})
     */
    public function handle(Request $request): Response
    {
        $args = (new Json($request->getContent()))->getValue();
        dump($args);
        $access_token = $this->github->auth($args['code'], $args['state']);


        dd($args);
        return new JsonResponse('');
    }
}
