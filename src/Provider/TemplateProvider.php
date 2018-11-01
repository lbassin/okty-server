<?php declare(strict_types=1);

namespace App\Provider;

use Symfony\Component\Yaml\Yaml;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class TemplateProvider
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
        $elements = [];

        $list = $this->github->getTree($this->path);
        foreach ($list as $data) {
            $elements[] = $this->getOne($data['name']);
        }

        return $elements;
    }

    public function getOne($template)
    {
        $file = $this->path . '/' . $template;
        if (substr_compare($file, '.yml', strlen($file) - 4, 4) !== 0) {
            $file .= '.yml';
        }

        $content = $this->github->getFile($file);

        $element = Yaml::parse($content, Yaml::PARSE_OBJECT);
        $element['id'] = pathinfo($template, PATHINFO_FILENAME);

        return $element;
    }

}
