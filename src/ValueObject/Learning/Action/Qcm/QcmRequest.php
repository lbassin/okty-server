<?php

declare(strict_types=1);

namespace App\ValueObject\Learning\Action\Qcm;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class QcmRequest
{
    private $questions;

    public function __construct(array $config)
    {
        $this->questions = [];
        foreach ($config as $question) {
            foreach ($question as $id => $response) {
                if (!is_bool($response)) {
                    throw new \LogicException('Response should be a boolean value');
                }
            }

            $this->questions[] = $question;
        }
    }

    public function getResponsesByQuestion(int $questionId): array
    {
        if (!isset($this->questions[$questionId])) {
            return [];
        }

        return array_keys(array_filter($this->questions[$questionId]));
    }
}
