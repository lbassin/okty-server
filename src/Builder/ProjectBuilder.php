<?php declare(strict_types=1);

namespace App\Builder;

use App\Builder\Resolver\FilesResolver;
use App\Builder\ValueObject\ContainerArgs;
use App\Builder\ValueObject\Project\DockerCompose;
use App\Builder\ValueObject\Project\Project;
use App\Event\Build\AfterBuildEvent;
use App\Event\Build\BeforeBuildEvent;
use App\Event\Build\BuildEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class ProjectBuilder
{
    private $composerBuilder;
    private $filesResolver;
    private $eventDispatcher;

    public function __construct(
        DockerComposerBuilder $composerBuilder,
        FilesResolver $filesResolver,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->composerBuilder = $composerBuilder;
        $this->filesResolver = $filesResolver;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function build(array $containers): Project
    {
        $files = [];
        $dockerCompose = new DockerCompose();

        $this->eventDispatcher->dispatch(BuildEvent::BEFORE_BUILD, new BeforeBuildEvent($containers));

        foreach ($containers as $container) {
            $containerArgs = new ContainerArgs($container);

            $this->composerBuilder->build($dockerCompose, $containerArgs);

            $files = array_merge($files, $this->filesResolver->resolve($containerArgs));
        }

        $project = new Project($dockerCompose, $files);

        $this->eventDispatcher->dispatch(BuildEvent::AFTER_BUILD, new AfterBuildEvent($project, $containers));

        return $project;
    }
}
