<?php

declare(strict_types=1);

namespace App\ValueObject\Service;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Id
{
    private $value;

    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new \LogicException("Id cannot be empty");
        }

        if (!preg_match('/^([a-zA-Z0-9]-?)*[a-zA-Z0-9]+$/', $value)) {
            throw new \LogicException("Id doesn't match the required format");
        }

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
