<?php declare(strict_types=1);

namespace App\Builder\Resolver;

use App\Builder\ValueObject\ContainerArgs;
use App\Repository\ContainerRepositoryInterface;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class ImageResolver
{
    private $containerRepository;

    public function __construct(ContainerRepositoryInterface $containerRepository)
    {
        $this->containerRepository = $containerRepository;
    }

    public function resolve(ContainerArgs $args): array
    {
        $output = [];

        $manifest = $this->containerRepository->findManifestByContainerId($args->getImage());

        if ($manifest->hasBuild()) {
            return ['build' => $manifest->getBuild()];
        }
        $output['image'] = $manifest->getImage();

        $tag = $manifest->getTag();
        $version = $args->getVersion();

        if (!empty($version)) {
            $tag = $version;
        }

        $output['image'] .= ':'.$tag;

        return $output;
    }
}
