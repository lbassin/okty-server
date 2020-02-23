<?php

declare(strict_types=1);

namespace App\Normalizer;

use App\Entity\Environment;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class EnvironmentNormalizer implements NormalizerInterface
{
    public function supportsNormalization($data, $format = null): bool
    {
        return $format === 'yaml' && $data instanceof Environment;
    }

    /** @var $environment Environment */
    public function normalize($environment, $format = null, array $context = []): string
    {
        return sprintf('%s=%s', $environment->getKey(), $environment->getValue());
    }
}
