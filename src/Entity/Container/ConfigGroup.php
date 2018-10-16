<?php declare(strict_types=1);

namespace App\Entity\Container;

class ConfigGroup
{
    /** @var integer */
    private $id;
    /** @var string */
    private $label;
    /** @var ConfigField[] */
    private $fields;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): ConfigGroup
    {
        $this->id = $id;
        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): ConfigGroup
    {
        $this->label = $label;
        return $this;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function setFields(array $fields): ConfigGroup
    {
        $this->fields = $fields;
        return $this;
    }
}
