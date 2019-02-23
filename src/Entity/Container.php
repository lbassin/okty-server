<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;

class Container
{
    /**
     * @Groups({"list"})
     */
    private $id;

    /**
     * @Groups({"list"})
     */
    private $name;

    /**
     * @Groups({"list"})
     */
    private $logo;

    private $config;

    public function __construct(string $id, string $name, string $logo, array $config = [])
    {
        $this->id = $id;
        $this->name = $name;
        $this->logo = $logo;
        $this->config = $config;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getLogo()
    {
        return $this->logo;
    }

    public function getConfig()
    {
        return $this->config;
    }
}
