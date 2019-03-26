<?php

declare(strict_types=1);

namespace App\Serializer;

use App\ValueObject\DockerCompose;
use App\ValueObject\Service;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class ComposeNormalizer implements NormalizerInterface
{
    private $serviceNormalizer;

    public function __construct(ServiceNormalizer $serviceNormalizer)
    {
        $this->serviceNormalizer = $serviceNormalizer;
    }

    public function normalize($compose, $format = null, array $context = [])
    {
        /** @var DockerCompose $compose */

        $services = [];
        /** @var Service $service */
        foreach ($compose->getServices() as $service) {
            $services[$service->getId()] = $this->serviceNormalizer->normalize($service);
        }

        return [
            'version' => $compose->getVersion(),
            'services' => $services,
        ];
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof DockerCompose;
    }
}
