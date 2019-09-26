<?php

declare(strict_types=1);

namespace App\Entity\Learning;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 *
 * @ORM\Entity();
 */
class Translation
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $locale;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $key;

    /**
     * @ORM\Column(type="string")
     */
    private $value;

    public function getId()
    {
        return $this->id;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getValue()
    {
        return $this->value;
    }
}
