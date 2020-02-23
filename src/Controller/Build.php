<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Project;
use App\Factory\ContainerFactory;
use App\ValueObject\Json;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class Build
{
    private $containerFactory;
    private $serializer;

    public function __construct(ContainerFactory $containerFactory, SerializerInterface $serializer)
    {
        $this->containerFactory = $containerFactory;
        $this->serializer = $serializer;
    }

    /**
     * @Route(path="/build", methods={"GET"})
     */
    public function handle(Request $request): Response
    {
        $payload = new Json($request->getContent());
        $project = new Project($this->containerFactory->buildAllFromRequestPayload($payload));

        return new JsonResponse(
            $this->serializer->serialize($project, 'yaml', ['yaml_inline' => 4]),
            Response::HTTP_OK,
            [],
            true
        );
    }
}
