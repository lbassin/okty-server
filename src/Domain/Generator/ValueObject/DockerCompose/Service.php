<?php

declare(strict_types=1);

namespace App\Domain\Generator\ValueObject\DockerCompose;

use App\Domain\Generator\ValueObject\DockerCompose\Service\Build;
use App\Domain\Generator\ValueObject\DockerCompose\Service\Environment;
use App\Domain\Generator\ValueObject\DockerCompose\Service\Id;
use App\Domain\Generator\ValueObject\DockerCompose\Service\Image;
use App\Domain\Generator\ValueObject\DockerCompose\Service\Port;
use App\Domain\Generator\ValueObject\DockerCompose\Service\Volume;

class Service
{
    /** @var Id */
    private $id;

    /** @var Image */
    private $image;

    /** @var Build */
    private $build;

    /** @var Port[] */
    private $ports;

    /** @var Volume[] */
    private $volumes;

    /** @var Environment[] */
    private $environments;
}