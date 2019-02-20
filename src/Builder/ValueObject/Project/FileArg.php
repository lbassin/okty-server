<?php

declare(strict_types=1);

namespace App\Builder\ValueObject\Project;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class FileArg implements \JsonSerializable
{
    private $key;
    private $value;

    public function __construct(string $key, string $value)
    {
        if (empty($key)) {
            throw new \LogicException("File arg key can't be empty");
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

    public function jsonSerialize()
    {
        return [
            'key' => $this->getKey(),
            'value' => $this->getValue(),
        ];
    }
}
