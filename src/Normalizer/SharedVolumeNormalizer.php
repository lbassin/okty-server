<?php

declare(strict_types=1);

namespace App\Normalizer;

use App\Entity\Volume;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SharedVolumeNormalizer implements NormalizerInterface
{
    public function supportsNormalization($data, $format = null): bool
    {
        return $format === 'yaml' && $data instanceof Volume\SharedVolume;
    }

    /** @var $volume Volume\SharedVolume */
    public function normalize($volume, $format = null, array $context = []): string
    {
        return sprintf('%s:%s', $volume->getSource(), $volume->getTarget());
    }
}
