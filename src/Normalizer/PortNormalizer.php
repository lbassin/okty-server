<?php

declare(strict_types=1);

namespace App\Normalizer;

use App\Entity\Port;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PortNormalizer implements NormalizerInterface
{
    public function supportsNormalization($data, $format = null): bool
    {
        return $format === 'yaml' && $data instanceof Port;
    }

    public function normalize($port, $format = null, array $context = []): string
    {
        return (string) $port;
    }
}
