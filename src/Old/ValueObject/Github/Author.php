<?php

declare(strict_types=1);

namespace App\ValueObject\Github;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Author
{
    private $name;
    private $email;

    public function __construct(string $name, string $email)
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('Name is required');
        }
        $this->name = $name;

        if (empty($email)) {
            throw new \InvalidArgumentException('Email is requieed');
        }
        $this->email = $email;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
