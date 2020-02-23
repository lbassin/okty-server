<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Container;
use App\ValueObject\Json;

class ContainerFactory
{
    private $imageFactory;
    private $portFactory;
    private $environmentFactory;
    private $volumeFactory;

    public function __construct(
        ImageFactory $imageFactory,
        PortFactory $portFactory,
        EnvironmentFactory $environmentFactory,
        VolumeFactory $volumeFactory
    ) {
        $this->portFactory = $portFactory;
        $this->environmentFactory = $environmentFactory;
        $this->volumeFactory = $volumeFactory;
        $this->imageFactory = $imageFactory;
    }

    public function buildOneFromRequest(array $request): Container
    {
        return new Container(
            $this->imageFactory->createAllFromRequest($request),
            $this->portFactory->createAllFromRequest($request),
            $this->environmentFactory->createAllFromRequest($request),
            $this->volumeFactory->createAllFromRequest($request)
        );
    }

    public function buildAllFromRequestPayload(Json $payload): array
    {
        $containers = [];
        foreach ($payload->getValue() as $containerData) {
            $containers[] = $this->buildOneFromRequest($containerData);
        }

        return $containers;
    }
}
