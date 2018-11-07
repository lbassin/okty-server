<?php declare(strict_types=1);

namespace App\Controller\Container;

use App\Builder\ProjectBuilder;
use App\Helper\ZipHelper;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

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

    public function handle(Request $request): Response
    {
        $args = json_decode($request->getContent(), true);
        if (!$args) {
            return new JsonResponse(['error' => 'JSON Syntax Error'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $files = $this->projectBuilder->build($args);
            if (empty($files)) {
                throw new \RuntimeException('No files generated');
            }

            $file = $this->zipHelper->zip($files);
        } catch (\RuntimeException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $response = new BinaryFileResponse($file);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'okty.zip');

        return $response;
    }
}
