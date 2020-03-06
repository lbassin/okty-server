<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Template;

interface TemplateRepositoryInterface
{
    public function getList(): array;

    public function getOne(string $name): Template;
}
