<?php declare(strict_types=1);

namespace App\Provider;

use Github\Exception\RuntimeException;
use GraphQL\Error\UserError;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Container
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
            $elements[] = $this->getFormConfig($data['name']);
        }

        return $elements;
    }

    public function getFormConfig($container): array
    {
        $file = $this->path . '/' . $container;

        try {
            $data = $this->github->getFile($file);
        } catch (RuntimeException $exception) {
            throw new UserError('Element not found');
        }

        $content = base64_decode($data['content'] ?? '');
        $element = Yaml::parse($content, Yaml::PARSE_OBJECT);
        $element['id'] = pathinfo($container, PATHINFO_FILENAME);

        return $element;
    }

    public function getManifest($container): array
    {
        //$content = $this->getContent()->download($this->githubUser, $this->githubRepo, $path, $this->githubBranch);
        return [];
    }

    public function getAllFilenames($container): array
    {
        return [];
    }

    public function getAllFileConfig($container): array
    {
        return [];
    }

    public function getFileConfig($container, $file): array
    {
        return [];
    }
}
