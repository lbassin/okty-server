<?php

declare(strict_types=1);

namespace App\ValueObject\Learning\Github;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Chapter
{
    private $filename;
    private $name;
    private $lessons;
    private $position;

    public function __construct(string $filename, array $config, int $position)
    {
        $this->name = [];
        $this->lessons = [];

        if (empty($filename)) {
            throw new \LogicException('Filename must contain a value');
        }
        $this->filename = $filename;

        if (!is_array($config['name'])) {
            throw new \LogicException('Name must be an array');
        }

        foreach ($config['name'] as $language => $value) {
            $this->name[$language] = $value;
        }

        if (!isset($this->name['en_US'])) {
            throw new \LogicException('English name must be filled');
        }

        if (!is_array($config['lessons'])) {
            throw new \LogicException('Lessons must be an array');
        }

        $this->lessons = $config['lessons'];

        if ($position <= 0) {
            throw new \LogicException('Position must be greater than zero');
        }
        $this->position = $position;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getAvailableLanguages(): array
    {
        return array_keys($this->name);
    }

    public function getNameByLanguage(string $language): string
    {
        if (!isset($this->name[$language])) {
            throw new \InvalidArgumentException('This language is not set');
        }

        return $this->name[$language];
    }

    public function getLessonsReferences(): array
    {
        return $this->lessons;
    }

    public function getPosition(): int
    {
        return $this->position;
    }
}
