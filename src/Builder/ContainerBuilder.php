<?php declare(strict_types=1);

namespace App\Builder;

use App\Provider\ContainerProvider;
use App\Provider\Github;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class ContainerBuilder
{
    private $github;
    private $container;

    public function __construct(Github $github, ContainerProvider $container)
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
        $resolverClassname = 'IsolatedResolver_' . uniqid();
        $resolverFullClassname = '\App\Builder\Tmp\\' . $resolverClassname;

        eval(<<<TXT
            namespace App\Builder\Tmp;
            class $resolverClassname { $resolvers }
TXT
        );

        $files = [];
        $warnings = [];
        foreach ($manifest['files'] as $file) {
            try {
                $content = $this->github->getFile($this->container->getPath($name) . 'sources/' . $file);
            } catch (FileNotFoundException $ex) {
                $warnings[] = $ex->getMessage();

                continue;
            }

            $resolver = new $resolverFullClassname();

            preg_match_all('/{{(?P<name>\w+)}}/', $content, $data);
            foreach ($data['name'] as $arg) {
                $defaultValue = $manifest['config'][$file]['args'][$arg]['default'] ?? '';
                $value = $args[$arg] ?? $defaultValue;

                if (method_exists($resolver, $arg)) {
                    $value = $resolver->{$arg}($args[$arg] ?? $value);
                }

                $content = str_replace('{{' . $arg . '}}', $value, $content);
            }

            $files[] = [
                'name' => ($manifest['config'][$file]['output'] ?? '') . $file,
                'content' => $content
            ];
        }

        return $files;
    }
}
