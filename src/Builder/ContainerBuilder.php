<?php declare(strict_types=1);

namespace App\Builder;

use App\Provider\Container;
use App\Provider\Github;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class ContainerBuilder
{
    private $github;
    private $container;

    public function __construct(Github $github, Container $container)
    {
        $this->github = $github;
        $this->container = $container;
    }

    public function build(string $name, array $args = []): array
    {
        $manifest = $this->container->getManifest($name);
        if (!isset($manifest['files'])) {
            return [];
        }

        $resolvers = $this->container->getResolvers($name);
        eval(<<<TXT
            namespace App\Builder\Tmp;
            class IsolatedResolver { $resolvers }
TXT
        );

        $files = [];
        foreach ($manifest['files'] as $file) {
            $content = $this->github->getFile($this->container->getPath($name) . 'sources/' . $file);

            if (!class_exists('\App\Builder\Tmp\IsolatedResolver')) {
                continue;
            }
            /** @noinspection PhpUndefinedClassInspection */
            /** @noinspection PhpUndefinedNamespaceInspection */
            /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
            $resolver = new \App\Builder\Tmp\IsolatedResolver();

            preg_match_all('/{{(?P<name>\w+)}}/', $content, $data);
            foreach ($data['name'] as $arg) {
                $value = $manifest['config'][$file]['args'][$arg]['default'] ?? '';

                if(method_exists($resolver, $arg)){
                    $value = $resolver->{$arg}($args[$arg] ?? $value);
                }

                $content = str_replace('{{' . $arg . '}}', $value, $content);
            }

            $files[] = [
                'output' => ($manifest['config'][$file]['output'] ?? '') . $file,
                'content' => $content
            ];
        }

        return $files;
    }
}
