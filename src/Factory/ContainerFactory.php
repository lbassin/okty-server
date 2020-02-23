<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Container;
use App\ValueObject\Id;
use App\ValueObject\Json;
use App\ValueObject\RequestArgs;
use InvalidArgumentException;

class ContainerFactory
{
    private $imageFactory;
    private $portFactory;
    private $environmentFactory;
    private $volumeFactory;
    private $optionFactory;

    public function __construct(
        ImageFactory $imageFactory,
        PortFactory $portFactory,
        EnvironmentFactory $environmentFactory,
        VolumeFactory $volumeFactory,
        OptionFactory $optionFactory
    ) {
        $this->portFactory = $portFactory;
        $this->environmentFactory = $environmentFactory;
        $this->volumeFactory = $volumeFactory;
        $this->imageFactory = $imageFactory;
        $this->optionFactory = $optionFactory;
    }

    public function buildOneFromRequest(string $template, RequestArgs $args): Container
    {
        if (empty($template)) {
            throw new InvalidArgumentException('A template is required to build a container');
        }

        return new Container(
            new Id($args->getId()),
            $this->imageFactory->create($template, $args->getVersion()),
            $this->portFactory->createAll($args->getPorts()),
            $this->environmentFactory->createAll($args->getEnvironments()),
            $this->volumeFactory->createAll($args->getVolumes()),
            $this->optionFactory->createAll($args->getOptions())
        );
    }

    public function buildAllFromRequestPayload(Json $payload): array
    {
        $containers = [];
        foreach ($payload->getAsArray() as $request) {
            $template = $request['template'] ?? '';
            $args = new RequestArgs($request['args'] ?? []);

            $containers[] = $this->buildOneFromRequest($template, $args);
        }

        return $containers;
    }
}
