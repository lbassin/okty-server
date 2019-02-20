<?php

declare(strict_types=1);

namespace App\Builder\ValueObject\Project;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Volume implements \JsonSerializable
{

    private $host;
    private $container;

    public function __construct(string $host, string $container)
    {
        if (empty($host)) {
            throw new \LogicException('Host source cannot be empty');
        }

        if (empty($container)) {
            throw new \LogicException("Cannot match $host to empty target");
        }

        if ($container[0] !== '/') {
            throw new \LogicException("Container path need to start from root folder");
        }

        $this->host = $host;
        $this->container = $container;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getContainer(): string
    {
        return $this->container;
    }

    public function jsonSerialize()
    {
        return [
            'host' => $this->getHost(),
            'container' => $this->getContainer(),
        ];
    }
}
