<?php

declare(strict_types=1);

namespace App\Event\Build;

use Symfony\Component\EventDispatcher\Event;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class BeforeBuildEvent extends Event
{
    private $containers;

    public function __construct(array $containers)
    {
        $this->containers = $containers;
    }

    public function getContainers(): array
    {
        return $this->containers;
    }
}
