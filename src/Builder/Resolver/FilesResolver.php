<?php declare(strict_types=1);

namespace App\Builder\Resolver;

use App\Builder\ValueObject\ContainerArgs;
use App\Service\Lambda;
use App\Repository\ContainerRepositoryInterface;
use App\ValueObject\Container\ManifestSourceConfig;
use App\ValueObject\File;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class FilesResolver
{
    private $containerRepository;
    private $lambdaHelper;

    public function __construct(ContainerRepositoryInterface $containerRepository, Lambda $lambdaHelper)
    {
        $this->containerRepository = $containerRepository;
        $this->lambdaHelper = $lambdaHelper;
    }

    public function resolve(ContainerArgs $containerArgs): array
    {
        $manifest = $this->containerRepository->findManifestByContainerId($containerArgs->getImage());
        if (!$manifest->hasFiles()) {
            return [];
        }

        $files = [];
        foreach ($manifest->getFiles() as $filename => $config) {
            $files[] = $this->buildFile($containerArgs, $filename, $config);
        }

        return $files;
    }

    private function buildFile(ContainerArgs $containerArgs, string $filename, ManifestSourceConfig $config): File
    {
        $file = $this->containerRepository->findSource($containerArgs->getImage(), $filename);
        $content = $file->getContent();

        preg_match_all('/{{(?P<arg>\w+)}}/', $content, $matches);
        foreach ($matches['arg'] as $arg) {
            $argInput = $containerArgs->getFileArgValue($arg) ?? $config->getDefaultValue($arg);

            $value = $this->lambdaHelper->invoke($containerArgs->getImage(), $arg, $argInput);

            $content = str_replace('{{'.$arg.'}}', $value, $content);
        }

        $output = $config->getOutput();
        $content = str_replace('\n', PHP_EOL, $content);

        return new File($output.$filename, $content);
    }
}
