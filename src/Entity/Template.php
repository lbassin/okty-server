<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Template
{
    private $id;
    private $name;
    private $logo;
    private $containers;

    public function __construct(string $id, string $name, string $logo, $containers = [])
    {
        $this->id = $id;
        $this->name = $name;
        $this->logo = $logo;
        $this->containers = $containers;
    }

    /**
     * @Groups({"list"})
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @Groups({"list"})
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @Groups({"list"})
     */
    public function getLogo(): string
    {
        return $this->logo;
    }

    public function getContainers(): array
    {
        return $this->containers;
    }
}
