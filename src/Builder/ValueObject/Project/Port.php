<?php

declare(strict_types=1);

namespace App\Builder\ValueObject\Project;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Port
{
    private $host;
    private $container;

    public function __construct(int $host, int $container)
    {
        if ($host < 0 || $host > 65535) {
            throw new \LogicException("Port $host out of range");
        }

        if ($container < 0 || $container > 65535) {
            throw new \LogicException("Port $container out of range");
        }

        $this->host = $host;
        $this->container = $container;
    }

    public function getHost(): int
    {
        return $this->host;
    }

    public function getContainer(): int
    {
        return $this->container;
    }

}
