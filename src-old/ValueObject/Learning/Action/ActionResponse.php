<?php

declare(strict_types=1);

namespace App\ValueObject\Learning\Action;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class ActionResponse
{
    private $validated;
    private $details;

    public function __construct(bool $validated, array $details)
    {
        $this->validated = $validated;
        $this->details = $details;
    }

    public function isValidated(): bool
    {
        return $this->validated;
    }

    public function getDetails(): array
    {
        return $this->details;
    }

}
