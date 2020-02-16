<?php declare(strict_types=1);

namespace App\Factory\Docker;

use App\ValueObject\Service\Args;
use App\ValueObject\Project;
use App\Event\Build\AfterBuildEvent;
use App\Event\Build\BeforeBuildEvent;
use App\Event\Build\BuildEvent;
use App\Factory\Docker\Resolver\FilesResolver;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class ProjectFactory
{
    private $composerFactory;
    private $filesResolver;
    private $eventDispatcher;

    public function __construct(
        ComposeFactory $composerFactory,
        FilesResolver $filesResolver,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->composerFactory = $composerFactory;
        $this->filesResolver = $filesResolver;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function build(array $args): Project
    {
        $files = [];

        $this->eventDispatcher->dispatch(BuildEvent::BEFORE_BUILD, new BeforeBuildEvent($args));

        $containers = [];
        foreach ($args as $container) {
            $containers[] = new Args($container);

            $files = array_merge($files, $this->filesResolver->resolve(end($containers)));
        }
        $compose = $this->composerFactory->build($containers);

        $project = new Project($compose, $files);

        $this->eventDispatcher->dispatch(BuildEvent::AFTER_BUILD, new AfterBuildEvent($project, $containers));

        return $project;
    }
}
