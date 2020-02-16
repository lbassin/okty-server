<?php declare(strict_types=1);

namespace App\Factory\Docker;

use App\ValueObject\Service\Args;
use App\ValueObject\DockerCompose;
use App\ValueObject\Service;
use App\Factory\Docker\Resolver\EnvironmentsResolver;
use App\Factory\Docker\Resolver\FilesResolver;
use App\Factory\Docker\Resolver\ImageResolver;
use App\Factory\Docker\Resolver\OptionsResolver;
use App\Factory\Docker\Resolver\PortsResolver;
use App\Factory\Docker\Resolver\VolumesResolver;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class ComposeFactory
{
    private $imageResolver;
    private $portsResolver;
    private $volumesResolver;
    private $environmentsResolver;
    private $optionsResolver;
    private $filesResolver;
    private $eventDispatcher;

    public function __construct(
        ImageResolver $imageResolver,
        PortsResolver $portsResolver,
        VolumesResolver $volumesResolver,
        EnvironmentsResolver $environmentsResolver,
        OptionsResolver $optionsResolver,
        FilesResolver $filesResolver,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->imageResolver = $imageResolver;
        $this->portsResolver = $portsResolver;
        $this->volumesResolver = $volumesResolver;
        $this->environmentsResolver = $environmentsResolver;
        $this->optionsResolver = $optionsResolver;
        $this->filesResolver = $filesResolver;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function build(array $containers): DockerCompose
    {
        $services = [];

        foreach ($containers as $args) {
            if (!$args instanceof Args) {
                throw new \LogicException('');
            }

            $id = $args->getId()->getValue();
            $image = $this->imageResolver->resolve($args);
            $options = $this->optionsResolver->resolve($args);
            $ports = $this->portsResolver->resolve($args);
            $volumes = $this->volumesResolver->resolve($args);
            $environments = $this->environmentsResolver->resolve($args);

            $services[] = new Service($id, $image, $options, $ports, $volumes, $environments);
        }

        return new DockerCompose($services);
    }
}
