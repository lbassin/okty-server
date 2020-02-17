<?php

declare(strict_types=1);

namespace App\Entity;

class Port
{
    private $host;
    private $container;
    private $localOnly;

    public function __construct(int $host, int $container, bool $localOnly = true)
    {
        $this->host = $host;
        $this->container = $container;
        $this->localOnly = $localOnly;
    }

    public function __toString()
    {
        $sourceAllowed = $this->localOnly ? '127.0.0.1:' : '';

        return sprintf('%s%d:%d', $sourceAllowed, $this->host, $this->container);
    }
}
