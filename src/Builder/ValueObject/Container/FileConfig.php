<?php

declare(strict_types=1);

namespace App\Builder\ValueObject\Container;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class FileConfig
{
    private $output;
    private $args;

    public function __construct(array $config)
    {
        if (empty($config['output'])) {
            throw new \LogicException('Output name is required');
        }
        $this->output = rtrim($config['output'], '/').'/';

        foreach ($config['args'] ?? [] as $name => $value) {
            $this->args[$name] = $value;
        }
    }

    public function getDefaultValue($name): string
    {
        return $this->args[$name]['default'] ?? '';
    }

    public function getOutput(): string
    {
        return $this->output;
    }
}
