<?php

declare(strict_types=1);

namespace App\Serializer;

use App\ValueObject\Service;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class ServiceNormalizer implements NormalizerInterface
{
    public function normalize($service, $format = null, array $context = [])
    {
        /** @var Service $service */

        $output = [];
        $output[$service->getImage() ? 'image' : 'build'] = $service->getImage() ? $service->getImage() : $service->getBuild();
        $output = array_merge($output, $service->getOptions());

        $output['ports'] = $service->getPorts();
        $output['volumes'] = $service->getVolumes();
        $output['environment'] = $service->getEnvironments();

        return array_filter($output);
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Service;
    }
}
