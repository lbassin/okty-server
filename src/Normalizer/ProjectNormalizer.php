<?php

declare(strict_types=1);

namespace App\Normalizer;

use App\Entity\Container;
use App\Entity\Project;
use App\Entity\Volume\DockerVolume;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ProjectNormalizer implements NormalizerInterface
{
    private $containerNormalizer;

    public function __construct(ContainerNormalizer $containerNormalizer)
    {
        $this->containerNormalizer = $containerNormalizer;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $format === 'yaml' && $data instanceof Project;
    }

    /**
     * @var Project $project
     */
    public function normalize($project, $format = null, array $context = []): array
    {
        return [
            'version' => $project->getVersion(),
            'containers' => $this->normalizeContainers($project),
            'volumes' => $this->normalizeVolumes($project),
        ];
    }

    private function normalizeContainers(Project $project): array
    {
        $containers = [];

        /** @var Container $container */
        foreach ($project->getContainers() as $container) {
            $containers[uniqid()] = $this->containerNormalizer->normalize($container);
        }

        return $containers;
    }

    private function normalizeVolumes(Project $project): array
    {
        $volumes = [];

        /** @var DockerVolume $volume */
        foreach ($project->getVolumes() as $volume) {
            $volumes[$volume->getName()] = null;
        }

        return $volumes;
    }
}
