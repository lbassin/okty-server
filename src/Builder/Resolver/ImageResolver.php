<?php declare(strict_types=1);

namespace App\Builder\Resolver;

use App\Builder\ValueObject\ContainerArgs;
use App\Provider\ContainerProvider;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class ImageResolver
{
    private $containerProvider;

    public function __construct(ContainerProvider $containerProvider)
    {
        $this->containerProvider = $containerProvider;
    }

    public function resolve(ContainerArgs $args): array
    {
        $output = [];

        $manifest = $this->containerProvider->getManifest($args->getImage());

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
