<?php declare(strict_types=1);

namespace App\Provider;

use Github\Exception\RuntimeException;
use GraphQL\Error\UserError;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Config
{
    private $containersPath;
    private $templatesPath;
    private $github;

    public function __construct(
        Github $github,
        string $containersPath,
        string $templatesPath
    )
    {
        $this->github = $github;
        $this->containersPath = $containersPath;
        $this->templatesPath = $templatesPath;
    }

    public function getAllContainers(): array
    {
        return $this->getAllElements($this->containersPath);
    }

    public function getContainer(string $id): array
    {
        return $this->getElement($this->containersPath, $id);
    }

    public function getAllTemplates(): array
    {
        return $this->getAllElements($this->templatesPath);
    }

    public function getTemplate(string $id): array
    {
        return $this->getElement($this->templatesPath, $id);
    }

    private function getAllElements($path): array
    {
        try {
            $list = $this->github->getTree($path);
        } catch (RuntimeException $exception) {
            throw new UserError('Element not found');
        }

        $elements = [];
        foreach ($list as $data) {
            $elements[] = $this->getElement($path, $data['name']);
        }

        return $elements;
    }

    private function getElement($path, $name): array
    {
        $file = $path . '/' . $name;

        try {
            $data = $this->github->getFile($file);
        } catch (RuntimeException $exception) {
            throw new UserError('Element not found');
        }

        $content = base64_decode($data['content'] ?? '');
        $element = Yaml::parse($content, Yaml::PARSE_OBJECT);
        $element['id'] = pathinfo($name, PATHINFO_FILENAME);

        return $element;
    }
}
