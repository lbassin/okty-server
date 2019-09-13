<?php

declare(strict_types=1);

namespace App\ValueObject;

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
            throw new \InvalidArgumentException('File name cannot be empty');
        }

        $this->name = $name;
        $this->content = $content ?? '';
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
