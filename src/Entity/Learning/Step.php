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
class Step
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     *
     * @Groups({"lesson_show"})
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     *
     * @Groups({"lesson_show"})
     */
    private $position;

    /**
     * @ORM\Column(type="text")
     *
     * @Groups({"lesson_show"})
     */
    private $text;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Learning\Lesson", inversedBy="steps")
     * @ORM\JoinColumn(nullable=false)
     */
    private $lesson;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Learning\Action")
     * @ORM\JoinColumn(nullable=true)
     *
     * @Groups({"lesson_show"})
     */
    private $action;

    public function __construct(string $id, int $position, string $text, Lesson $lesson, ?Action $action = null)
    {
        $this->id = $id;
        $this->position = $position;
        $this->text = $text;
        $this->lesson = $lesson;
        $this->action = $action;
    }

    public function getId(): string
    {
        return (string) $this->id;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getLesson(): Lesson
    {
        return $this->lesson;
    }

    public function getAction(): ?Action
    {
        return $this->action;
    }

    public function setAction(?Action $action): void
    {
        $this->action = $action;
    }
}
