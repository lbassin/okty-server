<?php declare(strict_types=1);

namespace App\Builder\Resolver;

use App\Provider\ContainerProvider;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class ImageResolver
{
    private $warnings = [];
    private $containerProvider;

    public function __construct(ContainerProvider $containerProvider)
    {
        $this->containerProvider = $containerProvider;
    }

    public function resolve(string $name, string $version): array
    {
        $output = [];

        try {
            $manifest = $this->containerProvider->getManifest($name);
        } catch (FileNotFoundException $ex) {
            $this->warnings[] = $ex->getMessage();

            return [];
        }

        if (!isset($manifest['docker'])) {
            $this->warnings[] = 'Image configuration missing';

            return [];
        }

        if (!empty($manifest['docker']['build'])) {
            return ['build' => $manifest['docker']['build']];
        }

        $output['image'] = $manifest['docker']['image'] ?? '';

        $tag = 'latest';
        if (!empty($manifest['docker']['tag'])) {
            $tag = $manifest['docker']['tag'];
        }

        if (!empty($version)) {
            $tag = $version;
        }

        $output['image'] .= ':' . $tag;

        return $output;
    }
}
