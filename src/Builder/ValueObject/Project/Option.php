<?php

declare(strict_types=1);

namespace App\Builder\ValueObject\Project;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Option implements \JsonSerializable
{
    private $key;
    private $value;

    private $ignoredKeys = [
        'id',
        'version',
        'volumes',
        'ports',
        'files',
        'environments',
        'image',
        'build'
    ];

    public function __construct(string $key, string $value)
    {
        if (empty($key)) {
            throw new \LogicException("Compose key can't be empty");
        }

        if (in_array($key, $this->ignoredKeys)) {
            throw new \LogicException("$key is a reserved word");
        }

        if (empty($value)) {
            throw new \LogicException("A value need to be set for $key entry");
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
