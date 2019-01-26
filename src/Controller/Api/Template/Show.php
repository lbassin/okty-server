<?php declare(strict_types=1);

namespace App\Controller\Api\Template;

use App\Provider\TemplateProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Show
{
    private $templateProvider;

    public function __construct(TemplateProvider $templateProvider)
    {
        $this->templateProvider = $templateProvider;
    }

    /**
     * @Route("templates/{id}", methods={"GET"}, requirements={"id": "^[a-zA-Z0-9]+$"})
     */
    public function handle(Request $request): Response
    {
        $templates = $this->templateProvider->getOne($request->attributes->get('id'));

        return new JsonResponse($templates);
    }
}
