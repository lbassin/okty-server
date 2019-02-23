<?php

declare(strict_types=1);

namespace App\Event\Build;

use App\Builder\ValueObject\ContainerArgs;
use App\Builder\ValueObject\Project\DockerCompose;
use App\Builder\ValueObject\Project\Service;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class AddContainerEvent extends Event
{
    private $project;
    private $args;
    private $service;

    public function __construct(DockerCompose $project, ContainerArgs $args, Service $service)
    {
        $this->project = $project;
        $this->args = $args;
        $this->service = $service;
    }

    public function getProject(): DockerCompose
    {
        return $this->project;
    }

    public function getArgs(): ContainerArgs
    {
        return $this->args;
    }

    public function getService(): Service
    {
        return $this->service;
    }
}
