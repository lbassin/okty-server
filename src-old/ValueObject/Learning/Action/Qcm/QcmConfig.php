<?php

declare(strict_types=1);

namespace App\ValueObject\Learning\Action\Qcm;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class QcmConfig
{
    private $questions;

    public function __construct(array $config)
    {
        $this->questions = [];

        foreach ($config['questions'] as $questionConfig) {
            $this->questions[] = new QcmConfigQuestion($questionConfig);
        }
    }

    public function getQuestions(): array
    {
        return $this->questions;
    }
}
