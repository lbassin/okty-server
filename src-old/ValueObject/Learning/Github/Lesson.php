<?php

namespace App\ValueObject\Learning\Github;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Lesson
{
    private $id;
    private $name;
    private $steps;
    private $position;

    public function __construct(array $config, int $position)
    {
        $this->id = [];
        $this->name = [];
        $this->steps = [];

        $this->initId($config);
        $this->initName($config);
        $this->initPosition($position);

        foreach ($config['steps'] as $step) {
            $this->steps[] = new Step($step);
        }
    }

    private function initId(array $config): void
    {
        if (!is_array($config['id'])) {
            throw new \LogicException('ID must be an array');
        }

        foreach ($config['id'] as $language => $value) {
            $this->id[$language] = $value;
        }

        if (!isset($this->id['en_US'])) {
            throw new \LogicException('English ID must be filled');
        }
    }

    private function initName(array $config): void
    {
        if (!is_array($config['name'])) {
            throw new \LogicException('Name must be an array');
        }

        foreach ($config['name'] as $language => $value) {
            $this->name[$language] = $value;
        }

        if (!isset($this->name['en_US'])) {
            throw new \LogicException('English name must be filled');
        }
    }

    private function initPosition(int $position): void
    {
        if ($position <= 0) {
            throw new \LogicException('Position must be greater than zero');
        }

        $this->position = $position;
    }

    public function getNameByLanguage(string $language): string
    {
        if (!isset($this->name[$language])) {
            throw new \InvalidArgumentException('This language is not set');
        }

        return $this->name[$language];
    }

    public function getIdByLanguage($language): string
    {
        if (!isset($this->name[$language])) {
            throw new \InvalidArgumentException('This language is not set');
        }

        return $this->id[$language];
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getSteps(): array
    {
        return $this->steps;
    }
}
