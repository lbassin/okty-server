<?php

declare(strict_types=1);

namespace App\Event\Build;

use App\ValueObject\Project;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class AfterBuildEvent extends Event
{
    private $containers;
    private $project;

    public function __construct(Project $project, array $containers)
    {
        $this->containers = $containers;
        $this->project = $project;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function getContainers(): array
    {
        return $this->containers;
    }
}
