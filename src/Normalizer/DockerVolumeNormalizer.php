<?php

declare(strict_types=1);

namespace App\Normalizer;

use App\Entity\Volume;
use App\Entity\Volume\DockerVolume;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class DockerVolumeNormalizer implements NormalizerInterface
{
    public function supportsNormalization($data, $format = null): bool
    {
        return $format === 'yaml' && $data instanceof Volume\DockerVolume;
    }

    /** @var $volume DockerVolume */
    public function normalize($volume, $format = null, array $context = []): array
    {
        return [
            'type' => 'volume',
            'source' => $volume->getName(),
            'target' => $volume->getTarget(),
        ];
    }
}
