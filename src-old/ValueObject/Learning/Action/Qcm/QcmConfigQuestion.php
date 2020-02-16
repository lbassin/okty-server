<?php

declare(strict_types=1);

namespace App\ValueObject\Learning\Action\Qcm;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class QcmConfigQuestion
{
    private $title;
    private $questions;
    private $responses;

    public function __construct(array $config)
    {
        $this->initTitle($config);
        $this->initQuestions($config);
        $this->initResponses($config);
    }

    private function initTitle(array $config): void
    {
        if (!isset($config['title'])) {
            throw new \LogicException('Title is missing');
        }

        $this->title = $config['title'];
    }

    private function initQuestions(array $config): void
    {
        if (empty($config['questions'])) {
            throw new \LogicException('At least one question is expected');
        }

        foreach ($config['questions'] as $question) {
            if (!is_string($question)) {
                throw new \LogicException('Question should be a string');
            }
        }

        $keys = range(1, count($config['questions']));

        $this->questions = array_combine($keys, $config['questions']);
    }

    private function initResponses(array $config): void
    {
        if (empty($config['responses'])) {
            throw new \LogicException('At least one response is expected');
        }

        foreach ($config['responses'] as $response) {
            $response = (int) $response;

            if (empty($this->questions[$response])) {
                throw new \LogicException('A response should be a reference to an existing question');
            }
        }

        $this->responses = $config['responses'];
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getQuestions(): array
    {
        return $this->questions;
    }

    public function getResponses(): array
    {
        return $this->responses;
    }
}
