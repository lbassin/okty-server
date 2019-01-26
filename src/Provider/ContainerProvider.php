<?php declare(strict_types=1);

namespace App\Provider;

use App\Builder\ValueObject\Container\Manifest;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class ContainerProvider
{
    private $github;
    private $path;

    public function __construct(Github $github, string $path)
    {
        $this->github = $github;
        $this->path = $path;
    }

    public function getList(): array
    {
        $elements = [];

        $list = $this->github->getTree($this->path);
        foreach ($list as $data) {
            $config = $this->getFormConfig($data['name']);

            $elements[] = [
                'id' => $config['id'],
                'name' => $config['name'],
                'logo' => $config['logo']
            ];
        }

        return $elements;
    }

    public function getFormConfig($container): array
    {
        $file = $this->path.'/'.$container.'/'.'form.yml';

        $content = $this->github->getFile($file);

        $element = Yaml::parse($content, Yaml::PARSE_OBJECT);
        $element['id'] = pathinfo($container, PATHINFO_FILENAME);

        return $element;
    }

    public function getManifest(string $container): Manifest
    {
        $content = $this->github->getFile($this->getPath($container).'manifest.yml');
        $element = Yaml::parse($content, Yaml::PARSE_OBJECT);

        return new Manifest($element);
    }

    public function getResolvers(string $container): string
    {
        try {
            $content = $this->github->getFile($this->getPath($container).'resolvers.php');

            return substr($content, 6); // Remove <?php
        } catch (FileNotFoundException $ex) {
            return '';
        }
    }

    public function getSource(string $container, string $file)
    {
        return $this->github->getFile($this->getPath($container).'sources/'.$file);
    }

    public function getPath(string $container)
    {
        return $this->path.'/'.$container.'/';
    }
}
