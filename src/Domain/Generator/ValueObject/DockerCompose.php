<?php

declare(strict_types=1);

namespace App\Domain\Generator\ValueObject;

use App\Domain\Generator\Exception\WrongServiceValueProvidedException;
use App\Domain\Generator\ValueObject\DockerCompose\Service;
use App\Domain\Generator\ValueObject\DockerCompose\Version;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class DockerCompose
{
    /** @var Version */
    private $version;

    /** @var Service[] */
    private $services;

    public function __construct(Version $version, array $services)
    {
        $this->version = $version;

        $this->services = [];
        foreach ($services as $service) {
            if (!$service instanceof Service) {
                throw new WrongServiceValueProvidedException(gettype($service));
            }

            $this->services[] = $service;
        }
    }

}
