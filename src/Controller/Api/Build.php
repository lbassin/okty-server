<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Builder\ProjectBuilder;
use App\Builder\ValueObject\Json;
use App\Helper\ZipHelper;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Build
{
    private $projectBuilder;
    private $zipHelper;

    public function __construct(ProjectBuilder $projectBuilder, ZipHelper $zipHelper)
    {
        $this->projectBuilder = $projectBuilder;
        $this->zipHelper = $zipHelper;
    }

    /**
     * @Route("build", methods={"POST"})
     */
    public function handle(Request $request): Response
    {
        $args = new Json($request->getContent());

        try {
            $project = $this->projectBuilder->build($args->getValue());
            $zip = $this->zipHelper->zip($project);
        } catch (\RuntimeException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\LogicException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        $response = new BinaryFileResponse($zip);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'okty.zip');

        return $response;
    }
}
