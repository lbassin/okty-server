<?php

declare(strict_types=1);

namespace App\Service\Learning\Validators;

use App\Entity\Learning\Action;
use App\ValueObject\Learning\Action\ActionResponse;

interface ValidatorInterface
{
    public function supports(string $type): bool;

    public function validate(Action $action, array $data): ActionResponse;
}
