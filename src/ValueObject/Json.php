<?php

declare(strict_types=1);

namespace App\ValueObject;

use JsonException;

class Json
{
    private $value;

    /**
     * @throws JsonException
     */
    public function __construct(string $json)
    {
        $value = json_decode($json, true, 12, JSON_THROW_ON_ERROR);

        $this->value = $value;
    }

    public function getAsArray(): array
    {
        return $this->value;
    }
}
