<?php

declare(strict_types=1);

namespace App\Entity\Learning;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 *
 * @ORM\Entity()
 * @ORM\Table(
 *      uniqueConstraints={
 *          @UniqueConstraint(name="chapter_position_unique", columns={"language", "position"})
 *      }
 * )
 */
class Chapter
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     *
     * @Groups({"list", "show"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"list", "show"})
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     *
     * @Groups({"show"})
     */
    private $position;

    /**
     * @ORM\Column(type="string", length=5)
     *
     * @Groups({"show"})
     */
    private $language;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Learning\Lesson", mappedBy="chapter", orphanRemoval=true)
     * @ORM\OrderBy({"position" = "ASC"})
     *
     * @Groups({"list", "show"})
     */
    private $lessons;

    public function __construct(string $id, string $name, int $position, string $language, ?Collection $lessons = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->position = $position;
        $this->language = $language;
        $this->lessons = $lessons ?? new ArrayCollection();
    }

    public function getId(): string
    {
        return (string) $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getLessons(): Collection
    {
        return $this->lessons;
    }
}
