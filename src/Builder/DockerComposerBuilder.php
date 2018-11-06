<?php declare(strict_types=1);

namespace App\Builder;

use App\Builder\Resolver\EnvironmentsResolver;
use App\Builder\Resolver\ImageResolver;
use App\Builder\Resolver\PortsResolver;
use App\Builder\Resolver\VolumesResolver;

class DockerComposerBuilder
{
    private $imageResolver;
    private $portsResolver;
    private $volumesResolver;
    private $environmentsResolver;

    public function __construct(
        ImageResolver $imageResolver,
        PortsResolver $portsResolver,
        VolumesResolver $volumesResolver,
        EnvironmentsResolver $environmentsResolver
    ) {
        $this->imageResolver = $imageResolver;
        $this->portsResolver = $portsResolver;
        $this->volumesResolver = $volumesResolver;
        $this->environmentsResolver = $environmentsResolver;
    }

    /**
     * If $output provided, if will merge new config with $output
     */
    public function build(string $name, array $args, array $output = []): array
    {
        $id = $args['id'] ?? $name;

        $container = $this->imageResolver->resolve($name, $args['version'] ?? '');
        $container['ports'] = $this->portsResolver->resolve($args['ports'] ?? []);
        $container['volumes'] = $this->volumesResolver->resolve($args['volumes'] ?? []);
        $container['environments'] = $this->environmentsResolver->resolve($args['environments'] ?? []);

        $container = array_filter($container);

        $output['version'] = $output['version'] ?? '3';
        $output['services'] = $output['services'] ?? [];
        $output['services'][$id] = $container;

        return $output;
    }
}
