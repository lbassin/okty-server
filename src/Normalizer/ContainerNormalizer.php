<?php

declare(strict_types=1);

namespace App\Normalizer;

use App\Entity\Container;
use App\Entity\Image\BuildImage;
use App\Entity\Volume\DockerVolume;
use App\Entity\Volume\SharedVolume;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ContainerNormalizer implements NormalizerInterface
{
    private $portNormalizer;
    private $sharedVolumeNormalizer;
    private $dockerVolumeNormalizer;
    private $environmentNormalizer;

    public function __construct(
        PortNormalizer $portNormalizer,
        SharedVolumeNormalizer $sharedVolumeNormalizer,
        DockerVolumeNormalizer $dockerVolumeNormalizer,
        EnvironmentNormalizer $environmentNormalizer

    ) {
        $this->portNormalizer = $portNormalizer;
        $this->sharedVolumeNormalizer = $sharedVolumeNormalizer;
        $this->dockerVolumeNormalizer = $dockerVolumeNormalizer;
        $this->environmentNormalizer = $environmentNormalizer;
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
        $imageKey = 'image';
        if ($container->getImage() instanceof BuildImage) {
            $imageKey = 'build';
        }

        $output[$imageKey] = (string) $container->getImage();

        $output += [
            'command' => $container->getCommand(),
            'working_dir' => $container->getWorkingDir(),
            'ports' => $this->normalizePorts($container),
            'volumes' => $this->normalizeVolumes($container),
            'environments' => $this->normalizeEnvironments($container),
        ];

        return array_filter($output);
    }

    private function normalizePorts(Container $container): array
    {
        $portNormalizer = $this->portNormalizer;

        return array_map(static function ($port) use ($portNormalizer) {
            return $portNormalizer->normalize($port);
        }, $container->getPorts());
    }

    private function normalizeVolumes(Container $container): array
    {
        $sharedVolumeNormalizer = $this->sharedVolumeNormalizer;
        $dockerVolumeNormalizer = $this->dockerVolumeNormalizer;

        return array_map(static function ($volume) use ($sharedVolumeNormalizer, $dockerVolumeNormalizer) {
            if ($volume instanceof SharedVolume) {
                return $sharedVolumeNormalizer->normalize($volume);
            }

            if ($volume instanceof DockerVolume) {
                return $dockerVolumeNormalizer->normalize($volume);
            }

            // TODO Add log
            return [];
        }, $container->getVolumes());
    }

    private function normalizeEnvironments(Container $container): array
    {
        $environmentNormalizer = $this->environmentNormalizer;

        $environments = array_map(static function ($environment) use ($environmentNormalizer) {
            return $environmentNormalizer->normalize($environment);
        }, $container->getEnvironments());

        return array_merge(...$environments);
    }
}
