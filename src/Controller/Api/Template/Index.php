<?php declare(strict_types=1);

namespace App\Controller\Api\Template;

use App\Provider\TemplateProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Index
{
    private $templateProvider;

    public function __construct(TemplateProvider $templateProvider)
    {
        $this->templateProvider = $templateProvider;
    }

    /**
     * @Route("templates", methods={"GET"})
     */
    public function handle(): Response
    {
        $templates = $this->templateProvider->getList();

        return new JsonResponse($templates);
    }
}
