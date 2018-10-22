<?php declare(strict_types=1);

namespace App\Builder;

use App\Builder\Validator\Port;
use App\Builder\Validator\Volume;
use App\Provider\ContainerProvider;
use App\Provider\Github;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Yaml\Yaml;

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

        $compose = $this->generateDockerCompose($args);
        $files = $this->generateFiles($name, $args['files'] ?? []);

        return array_merge([$compose], $files);
    }

    private function generateDockerCompose(array $args): array
    {
        $output = ['version' => '3', 'services' => []];

        $container = [];
        $container['ports'] = $this->validateData($args['ports'], new Port());
        $container['volumes'] = $this->validateData($args['volumes'], new Volume());

        $output['services'][$args['id']] = $container;

        return [
            'name' => 'docker-compose',
            'content' => Yaml::dump($output)
        ];
    }

    private function validateData(array $data, Constraint $constraint): array
    {
        $config = [];
        foreach ($data as $value) {
            $errors = $this->validator->validate($value, $constraint);
            if (count($errors) > 0) {
                continue;
            }

            $config[] = $value;
        }

        return $config;
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
