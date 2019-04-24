<?php

declare(strict_types=1);

namespace App\ValueObject\Learning;

use App\ValueObject\Learning\QcmConfig\Question;

class QcmConfig
{
    private $questions;

    public function __construct(array $config)
    {
        $this->questions = [];

        foreach ($config as $questionConfig) {
            $this->questions[] = new Question($questionConfig);
        }
    }

}
