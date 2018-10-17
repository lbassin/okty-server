<?php declare(strict_types=1);

namespace App\Provider;

use Github\Exception\RuntimeException;
use GraphQL\Error\UserError;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Template
{
    private $github;
    private $path;

    public function __construct(Github $github, string $path)
    {
        $this->github = $github;
        $this->path = $path;
    }

    public function getAll()
    {
        try {
            $list = $this->github->getTree($this->path);
        } catch (RuntimeException $exception) {
            throw new UserError('Element not found');
        }

        $elements = [];
        foreach ($list as $data) {
            $elements[] = $this->getOne($data['name']);
        }

        return $elements;
    }

    public function getOne($template)
    {
        $file = $this->path . '/' . $template;

        try {
            $content = $this->github->getFile($file);
        } catch (RuntimeException $exception) {
            throw new UserError('Element not found');
        }

        $element = Yaml::parse($content, Yaml::PARSE_OBJECT);
        $element['id'] = pathinfo($template, PATHINFO_FILENAME);

        return $element;
    }

}
