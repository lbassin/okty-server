<?php

declare(strict_types=1);

namespace App\Controller\Api\Learning;

use App\Repository\Learning\ActionRepositoryInterface;
use App\Service\Learning\ActionValidator;
use App\ValueObject\Json;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Check
{
    private $actionValidator;
    private $actionRepository;
    private $serializer;

    public function __construct(
        ActionValidator $actionValidator,
        ActionRepositoryInterface $actionRepository,
        SerializerInterface $serializer
    ) {
        $this->actionValidator = $actionValidator;
        $this->actionRepository = $actionRepository;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/learning/check", methods={"POST"})
     */
    public function handle(Request $request): Response
    {
        $data = (new Json($request->getContent()))->getValue();
        $action = $this->actionRepository->findById($data['id']);

        $result = $this->actionValidator->validate($action, $data['values']);

        return new JsonResponse(
            $this->serializer->serialize($result, 'json'),
            Response::HTTP_OK,
            [],
            true
        );
    }
}
