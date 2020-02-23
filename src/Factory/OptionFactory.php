<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Option;

class OptionFactory
{
    public function createAll(array $options): array
    {
        return array_map(function ($option) {
            return new Option($option['name'], $option['value']);
        }, $options);
    }
}
