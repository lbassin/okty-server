<?php

declare(strict_types=1);

namespace App\Builder\ValueObject\Project;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class File
{
    private $name;
    private $content;

    public function __construct(string $name, string $content)
    {
        if (empty($name)) {
            throw new \LogicException('File name cannot be empty');
        }

        if (empty($content)) {
            throw new \LogicException('File content cannot be empty');
        }

        $this->name = $name;
        $this->content = $content;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
