<?php declare(strict_types=1);

namespace App\Factory\Docker\Resolver;

use App\Repository\ContainerRepositoryInterface;
use App\ValueObject\Container\Manifest;
use App\ValueObject\Service\Args;
use Exception;

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

    public function resolve(Args $args): array
    {
        $output = [];

        try {
            $manifest = $this->containerRepository->findManifestByContainerId($args->getImage());
        } catch (Exception $exception) {
            if (!$args->isFromBuilder()) {
                throw $exception;
            }

            $manifest = new Manifest(['image' => $args->getImage(), 'tag' => $args->getVersion()]);
        }

        if ($manifest->hasBuild()) {
            return ['build' => $manifest->getBuild()];
        }
        $output['image'] = $manifest->getImage();

        $tag = $manifest->getTag();
        $version = $args->getVersion();

        if (!empty($version)) {
            $tag = $version;
        }

        $output['image'] .= $tag ? ':'.$tag : '';

        return $output;
    }
}
