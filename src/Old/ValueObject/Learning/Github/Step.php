<?php

declare(strict_types=1);

namespace App\ValueObject\Learning\Github;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Step
{
    private $text;
    private $action;

    public function __construct(array $config)
    {
        $this->text = [];

        if (!is_array($config['text'])) {
            throw new \LogicException('Text must be an array');
        }

        foreach ($config['text'] as $language => $value) {
            $this->text[$language] = $value;
        }

        if (!isset($this->text['en_US'])) {
            throw new \LogicException('English name must be filled');
        }

        if (!empty($config['action'])) {
            $this->action = new Action($config['action']);
        }
    }

    public function getTextByLanguage(string $language): string
    {
        if (!isset($this->text[$language])) {
            throw new \InvalidArgumentException('This language is not set');
        }

        return $this->text[$language];
    }

    public function getAction(): ?Action
    {
        return $this->action;
    }
}
