<?php

declare(strict_types=1);

namespace App\Entity\Learning;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 *
 * @ORM\Entity()
 */
class Lesson
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     *
     * @Groups({"chapter_list", "chapter_show", "lesson_list", "lesson_show"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"chapter_list", "chapter_show", "lesson_list", "lesson_show"})
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     *
     * @Groups({"lesson_show"})
     */
    private $position;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Learning\Chapter", inversedBy="lessons")
     * @ORM\JoinColumn(nullable=false)
     */
    private $chapter;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Learning\Step", mappedBy="lesson", orphanRemoval=true)
     * @ORM\OrderBy({"position" = "ASC"})
     *
     * @Groups({"lesson_show"})
     */
    private $steps;

    public function __construct(string $id, string $name, int $position, Chapter $chapter, ?Collection $steps = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->position = $position;
        $this->chapter = $chapter;
        $this->steps = $steps ?? new ArrayCollection();
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

    public function getChapter(): Chapter
    {
        return $this->chapter;
    }

    public function getSteps(): Collection
    {
        return $this->steps;
    }
}
