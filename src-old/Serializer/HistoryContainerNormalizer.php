<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\HistoryContainer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class HistoryContainerNormalizer implements NormalizerInterface
{
    private $normalizer;

    public function __construct(ObjectNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function normalize($container, $format = null, array $context = [])
    {
        /** @var HistoryContainer $container */
        return [
            'image' => $container->getImage(),
            'args' => $container->getArgs(),
        ];
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof HistoryContainer;
    }
}
