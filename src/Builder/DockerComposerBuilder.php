<?php declare(strict_types=1);

namespace App\Builder;

use App\Builder\Resolver\EnvironmentsResolver;
use App\Builder\Resolver\FilesResolver;
use App\Builder\Resolver\ImageResolver;
use App\Builder\Resolver\OptionsResolver;
use App\Builder\Resolver\PortsResolver;
use App\Builder\Resolver\VolumesResolver;
use App\Builder\ValueObject\ContainerArgs;
use App\Builder\ValueObject\Project\DockerCompose;
use App\Builder\ValueObject\Project\Service;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class DockerComposerBuilder
{
    private $imageResolver;
    private $portsResolver;
    private $volumesResolver;
    private $environmentsResolver;
    private $optionsResolver;
    private $filesResolver;

    public function __construct(
        ImageResolver $imageResolver,
        PortsResolver $portsResolver,
        VolumesResolver $volumesResolver,
        EnvironmentsResolver $environmentsResolver,
        OptionsResolver $optionsResolver,
        FilesResolver $filesResolver
    ) {
        $this->imageResolver = $imageResolver;
        $this->portsResolver = $portsResolver;
        $this->volumesResolver = $volumesResolver;
        $this->environmentsResolver = $environmentsResolver;
        $this->optionsResolver = $optionsResolver;
        $this->filesResolver = $filesResolver;
    }

    public function build(DockerCompose &$project, ContainerArgs $args): void
    {
        $id = $args->getId()->getValue();
        $image = $this->imageResolver->resolve($args);
        $options = $this->optionsResolver->resolve($args);
        $ports = $this->portsResolver->resolve($args);
        $volumes = $this->volumesResolver->resolve($args);
        $environments = $this->environmentsResolver->resolve($args);

        $container = new Service($id, $image, $options, $ports, $volumes, $environments);

        $project->addService($container);
    }
}
