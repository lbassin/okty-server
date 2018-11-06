<?php declare(strict_types=1);

namespace App\Builder;

use App\Builder\Resolver\FileResolver;
use App\Provider\ContainerProvider;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class SourcesBuilder
{
    private $warnings = [];
    private $containerProvider;
    private $fileResolver;

    public function __construct(ContainerProvider $containerProvider, FileResolver $fileResolver)
    {
        $this->containerProvider = $containerProvider;
        $this->fileResolver = $fileResolver;
    }

    public function build(string $image, array $config): array
    {
        try {
            $manifest = $this->containerProvider->getManifest($image);
        } catch (FileNotFoundException $ex) {
            $this->warnings[] = $ex->getMessage();

            return [];
        }

        if (!isset($manifest['files'])) {
            return [];
        }

        $files = [];
        foreach ($manifest['files'] as $file) {
            $files[] = $this->fileResolver->resolve($image, $manifest, $file, $config);
        }

        return $files;
    }
}
