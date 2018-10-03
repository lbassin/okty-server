<?php declare(strict_types=1);

namespace App\Provider\Config;

interface ConfigProvider
{
    public function getAllContainers(): array;

    public function getAllTemplates(): array;

    public function getTemplate(string $id): array;
}
