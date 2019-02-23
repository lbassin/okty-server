<?php declare(strict_types=1);

namespace App\Provider;

use App\Builder\ValueObject\Container\Manifest;
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

    public function getManifest(string $container): Manifest
    {
        $content = $this->github->getFile($this->getPath($container).'manifest.yml');
        $element = Yaml::parse($content, Yaml::PARSE_OBJECT);

        return new Manifest($element);
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
