<?php declare(strict_types=1);

namespace App\Controller\Template;

use App\Provider\TemplateProvider;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

    public function handle(Request $request): Response
    {
        $id = $request->attributes->get('id');

        try {
            $templates = $this->templateProvider->getOne($id);
        } catch (FileNotFoundException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($templates);
    }
}
