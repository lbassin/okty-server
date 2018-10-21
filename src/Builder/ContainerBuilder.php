<?php declare(strict_types=1);

namespace App\Builder;

use App\Builder\Validator\Port;
use App\Provider\ContainerProvider;
use App\Provider\Github;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class ContainerBuilder
{
    private $github;
    private $container;
    private $validator;

    public function __construct(
        Github $github,
        ContainerProvider $container,
        ValidatorInterface $validator
    )
    {
        $this->github = $github;
        $this->container = $container;
        $this->validator = $validator;
    }

    private function getArgsDevData(): array
    {
        // To remove
        return [
            'id' => 'nginx',
            'ports' => [
                '8080:80'
            ],
            'volumes' => [
                './:/app'
            ],
            'environment' => [
                'MYSQL_PASSWORD=ok'
            ],
            'files' => [
                'root_folder' => 'public',
                'php_container_link' => 'php'
            ]
        ];
    }

    public function build(string $name, array $args = []): array
    {
        $args = $this->getArgsDevData();

//        $files = $this->generateFiles($name, $args['files'] ?? []);
        $compose = $this->generateDockerCompose($name, $args);

        return [];
    }

    private function generateDockerCompose(string $name, array $args): string
    {
        $output = ['version' => '3', 'services' => [], 'volumes' => []];

        foreach ($args['ports'] as $port) {
            $errors = $this->validator->validate($port, new Port());
            print_r($errors);
        }

        print_r($output);
        return '';
    }

    private function generateFiles(string $name, array $args): array
    {
        $manifest = $this->container->getManifest($name);
        if (!isset($manifest['files'])) {
            return [];
        }

        $resolvers = $this->container->getResolvers($name);
        $resolverClassname = 'IsolatedResolver_' . uniqid();
        $resolverFullClassname = '\App\Builder\Tmp\\' . $resolverClassname;

        eval("namespace App\Builder\Tmp; class $resolverClassname { $resolvers }");

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
