<?php

declare(strict_types=1);

namespace App\Serializer;

use App\ValueObject\File;
use App\ValueObject\Project;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class ProjectNormalizer implements NormalizerInterface
{
    public function normalize($project, $format = null, array $context = [])
    {
        /** @var Project $project */

        $files = $project->getFiles();
        $compose = $project->getDockerCompose();

        $composeContent = $compose->__toString();

        return array_merge([new File('docker-compose.yml', $composeContent)], $files);
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Project;
    }
}
