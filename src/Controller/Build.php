<?php

declare(strict_types=1);

namespace App\Controller;

use App\Factory\ContainerFactory;
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
        $payload = json_decode($request->getContent(), true);

        $containers = [];
        foreach ($payload as $data) {

            // TODO Assert template is provided

            $containers[] = $this->containerFactory->buildFromRequest($data);
        }

        $data = $this->serializer->serialize($containers, 'yaml');

        dd($data);

        return new Response('test');
    }
}
