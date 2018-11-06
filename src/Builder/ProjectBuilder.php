<?php declare(strict_types=1);

namespace App\Builder;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class ProjectBuilder
{
    private $warnings = [];
    private $composerBuilder;
    private $sourcesBuilder;

    public function __construct(DockerComposerBuilder $composerBuilder, SourcesBuilder $sourcesBuilder)
    {
        $this->composerBuilder = $composerBuilder;
        $this->sourcesBuilder = $sourcesBuilder;
    }

    public function build(array $containers): array
    {
        $output = [];
        $compose = [];

        foreach ($containers as $config) {
            if (empty($config['image'])) {
                $this->warnings[] = 'No image specified';

                continue;
            }

            $config['args'] = $config['args'] ?? [];
            $config['args']['files'] = $config['args']['files'] ?? [];

            $compose = $this->composerBuilder->build($config['image'], $config['args'], $compose);
            $files = $this->sourcesBuilder->build($config['image'], $config['args']['files']);

            $output = array_merge($output, $files);
        }

        return array_merge([$compose], $output);
    }
}
