<?php

declare(strict_types=1);

namespace App\Domain\Generator\ValueObject\DockerCompose\Service;

use App\Domain\Generator\Exception\DockerCompose\Service\Id\EmptyServiceIdException;
use App\Domain\Generator\Exception\DockerCompose\Service\Id\WrongServiceIdFormatException;

class Id
{
    private $value;

    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new EmptyServiceIdException();
        }

        if (!preg_match('/^([a-zA-Z0-9]-?)*[a-zA-Z0-9]+$/', $value)) {
            throw new WrongServiceIdFormatException("Id doesn't match the required format");
        }

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

}
