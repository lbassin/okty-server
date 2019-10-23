<?php

declare(strict_types=1);

namespace App\ValueObject\Learning\Github;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Action
{
    private $type;
    private $config;

    public function __construct(array $config)
    {
        if (!in_array($config['type'], ['qcm'])) {
            throw new \LogicException('Action type is not allowed : '.$config['type']);
        }
        $this->type = $config['type'];

        if (empty($config['config']) || !is_array($config['config'])) {
            throw new \LogicException('Action needs a configuration');
        }
        $this->config = $config['config'];
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getConfig(string $language): array
    {
        $functionName = 'getConfig'.ucfirst($this->type);
        if (!method_exists($this, $functionName)) {
            throw new \LogicException('Requested type do not exist : '.$this->type);
        }

        return $this->{$functionName}($language);
    }

    public function getConfigQcm(string $language): array
    {
        $labels = [];
        foreach ($this->config['questions'] as $config) {
            $labels[] = [
                'title' => $config['title'][$language],
                'questions' => $config['questions'][$language],
                'responses' => $config['responses'],
            ];
        }

        return [
            'questions' => $labels,
        ];
    }
}
