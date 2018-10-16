<?php declare(strict_types=1);

namespace App\Entity\Container;

class ConfigField
{
    /** @var integer */
    private $id;
    /** @var string */
    private $label;
    /** @var string */
    private $type;
    /** @var string */
    private $base;
    /** @var string */
    private $destination;
    /** @var string */
    private $value;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): ConfigField
    {
        $this->id = $id;
        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): ConfigField
    {
        $this->label = $label;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): ConfigField
    {
        $this->type = $type;
        return $this;
    }

    public function getBase(): string
    {
        return $this->base;
    }

    public function setBase(string $base): ConfigField
    {
        $this->base = $base;
        return $this;
    }

    public function getDestination(): string
    {
        return $this->destination;
    }

    public function setDestination(string $destination): ConfigField
    {
        $this->destination = $destination;
        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): ConfigField
    {
        $this->value = $value;
        return $this;
    }
}
