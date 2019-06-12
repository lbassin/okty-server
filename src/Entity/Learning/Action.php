<?php

declare(strict_types=1);

namespace App\Entity\Learning;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 *
 * @ORM\Entity()
 */
class Action
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     *
     * @Groups({"step_show"})
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     *
     * @Groups({"step_show"})
     */
    private $type;

    /**
     * @ORM\Column(type="json")
     *
     * @Groups({"step_show"})
     */
    private $config;

    /**
     * @ORM\Column(type="string", length=5)
     *
     * @Groups({"step_show"})
     */
    private $language;

    public function __construct(string $id, string $type, array $config, string $language)
    {
        $this->id = $id;
        $this->type = $type;
        $this->config = $config;
        $this->language = $language;
    }

    public function getId(): string
    {
        return (string) $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }
}
