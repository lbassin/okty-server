<?php

declare(strict_types=1);

namespace App\Serializer;

use App\ValueObject\Service\Args;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class ContainerArgsNormalizer implements NormalizerInterface
{
    private $objectNormalizer;

    private $format;
    private $context;

    public function __construct(ObjectNormalizer $objectNormalizer)
    {
        $this->objectNormalizer = $objectNormalizer;
    }

    public function normalize($container, $format = null, array $context = [])
    {
        /** @var Args $container */

        $this->format = $format;
        $this->context = $context;

        return [
            'id' => $container->getId()->getValue(),
            'version' => $container->getVersion(),
            'compose' => array_map([$this, 'normalizeObject'], $container->getComposeOptions()),
            'ports' => array_map([$this, 'normalizeObject'], $container->getPorts()),
            'volumes' => array_map([$this, 'normalizeObject'], $container->getVolumes()),
            'environments' => array_map([$this, 'normalizeObject'], $container->getEnvironments()),
            'fileArgs' => array_map([$this, 'normalizeObject'], $container->getFileArgs()),
        ];
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Args;
    }

    private function normalizeObject($object)
    {
        return $this->objectNormalizer->normalize($object, $this->format, $this->context);
    }
}
