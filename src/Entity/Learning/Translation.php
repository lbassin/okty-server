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
     * @ORM\Column(type="string", length=255, name="translation_key")
     */
    private $key;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $value;

    public function __construct(string $id, string $locale, string $key, string $value)
    {
        $this->id = $id;
        $this->locale = $locale;
        $this->key = $key;
        $this->value = $value;
    }

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
