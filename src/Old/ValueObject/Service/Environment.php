<?php

declare(strict_types=1);

namespace App\ValueObject\Service;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Environment
{

    private $key;
    private $value;

    public function __construct(string $key, string $value)
    {
        if (empty($key)) {
            throw new \LogicException("Environment key can't be empty");
        }

        if (empty($value)) {
            throw new \LogicException("A value need to be set for env $key");
        }

        $this->key = $key;
        $this->value = $value;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
