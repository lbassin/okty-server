<?php declare(strict_types=1);

namespace App\Provider;

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

    public function getSource(string $container, string $file)
    {
        return $this->github->getFile($this->getPath($container).'sources/'.$file);
    }

    public function getPath(string $container)
    {
        return $this->path.'/'.$container.'/';
    }
}
