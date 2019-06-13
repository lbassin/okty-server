<?php

declare(strict_types=1);

namespace App\ValueObject\Github;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Target
{
    private $branch;
    private $message;

    public function __construct(string $branch, string $message)
    {
        if (empty($branch)) {
            throw new \InvalidArgumentException('Branch name is required');
        }

        if (in_array($branch, ['dev'])) {
            throw new \InvalidArgumentException('Specified branch name is not allowed');
        }
        $this->branch = $branch;

        if (empty($message)) {
            throw new \InvalidArgumentException('Commit message is required');
        }
        $this->message = $message;
    }

    public function getCommitMessage(): string
    {
        return $this->message;
    }
}
