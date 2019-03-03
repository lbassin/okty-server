<?php

declare(strict_types=1);

namespace App\Service;

interface LambdaInterface
{
    public function invoke($function, $resolver, $arg): string;
}
