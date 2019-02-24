<?php

namespace App\Controller;

use App\Exception\BadCredentialsException;
use App\Exception\FileNotFoundException;
use Symfony\Bundle\TwigBundle\Controller\ExceptionController;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Exception extends ExceptionController
{
    public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null): Response
    {
        if ($exception->getClass() === FileNotFoundException::class) {
            return new JsonResponse(['error' => $exception->getMessage()], $exception->getCode());
        }

        if ($exception->getClass() === BadCredentialsException::class) {
            return new JsonResponse(['error' => $exception->getMessage()], $exception->getCode());
        }

        if ($exception->getClass() === \LogicException::class) {
            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return parent::showAction($request, $exception, $logger);
    }
}
