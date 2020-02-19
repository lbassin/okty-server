<?php

declare(strict_types=1);

namespace App\Normalizer;

use App\Entity\Container;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ContainerNormalizer implements NormalizerInterface
{
    private $portNormalizer;

    public function __construct(PortNormalizer $portNormalizer)
    {
        $this->portNormalizer = $portNormalizer;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $format === 'yaml' && $data instanceof Container;
    }

    /**
     * @var Container $container
     */
    public function normalize($container, $format = null, array $context = []): array
    {
        return [
            'ports' => $this->normalizePorts($container),
        ];
    }

    private function normalizePorts(Container $container): array
    {
        $portNormalizer = $this->portNormalizer;

        return array_map(static function ($port) use ($portNormalizer) {
            return $portNormalizer->normalize($port);
        }, $container->getPorts());
    }
}
