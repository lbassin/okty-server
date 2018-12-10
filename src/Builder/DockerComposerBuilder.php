<?php declare(strict_types=1);

namespace App\Builder;

use App\Builder\Resolver\EnvironmentsResolver;
use App\Builder\Resolver\ImageResolver;
use App\Builder\Resolver\OptionsResolver;
use App\Builder\Resolver\PortsResolver;
use App\Builder\Resolver\VolumesResolver;

class DockerComposerBuilder
{
    private $imageResolver;
    private $portsResolver;
    private $volumesResolver;
    private $environmentsResolver;
    private $optionsResolver;

    public function __construct(
        ImageResolver $imageResolver,
        PortsResolver $portsResolver,
        VolumesResolver $volumesResolver,
        EnvironmentsResolver $environmentsResolver,
        OptionsResolver $optionsResolver
    ) {
        $this->imageResolver = $imageResolver;
        $this->portsResolver = $portsResolver;
        $this->volumesResolver = $volumesResolver;
        $this->environmentsResolver = $environmentsResolver;
        $this->optionsResolver = $optionsResolver;
    }

    /**
     * If $output provided, if will merge new config with $output
     */
    public function build(string $name, array $args, array $output = []): array
    {
        $id = $args['id'] ?? $name;

        $container = [];

        $image = $this->imageResolver->resolve($name, $args['version'] ?? '');
        $container = array_merge($container, $image);

        $options = $this->optionsResolver->resolve($args['compose'] ?? []);
        $container = array_merge($container, $options);

        $container['ports'] = $this->portsResolver->resolve($args['ports'] ?? []);
        $container['volumes'] = $this->volumesResolver->resolve($args['volumes'] ?? []);
        $container['environment'] = $this->environmentsResolver->resolve($args['environments'] ?? []);

        $container = array_filter($container);

        $output['version'] = $output['version'] ?? '3';
        $output['services'] = $output['services'] ?? [];
        $output['services'][$id] = $container;

        return $output;
    }
}
