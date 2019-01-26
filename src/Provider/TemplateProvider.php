<?php declare(strict_types=1);

namespace App\Provider;

use App\Exception\BadCredentialsException;
use App\Exception\FileNotFoundException;
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

    /**
     * @throws BadCredentialsException
     * @throws FileNotFoundException
     */
    public function getList()
    {
        $elements = [];

        $list = $this->github->getTree($this->path);
        foreach ($list as $data) {
            $config = $this->getOne($data['name']);

            $elements[] = [
                'id' => $config['id'],
                'name' => $config['name'],
                'logo' => $config['logo'],
            ];
        }

        return $elements;
    }

    /**
     * @throws BadCredentialsException
     * @throws FileNotFoundException
     */
    public function getOne($template)
    {
        $file = $this->path.'/'.$template;
        if (substr_compare($file, '.yml', strlen($file) - 4, 4) !== 0) {
            $file .= '.yml';
        }

        $content = $this->github->getFile($file);

        $element = Yaml::parse($content, Yaml::PARSE_OBJECT);
        $element['id'] = pathinfo($template, PATHINFO_FILENAME);

        return $element;
    }

}
