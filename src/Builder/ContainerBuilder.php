<?php declare(strict_types=1);

namespace App\Builder;

use App\Builder\Validator\Environment;
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

    private $warnings;

    public function __construct(
        Github $github,
        ContainerProvider $container,
        ValidatorInterface $validator
    )
    {
        $this->github = $github;
        $this->container = $container;
        $this->validator = $validator;
        $this->warnings = [];
    }

    public function buildAll(array $data): array
    {
        $output = [];
        $compose = [];

        foreach ($data as $config) {
            if (empty($config['image']) || empty($config['args'])) {
                continue;
            }

            $files = $this->build($config['image'], $config['args']);
            $compose = $this->mergeCompose($compose, array_shift($files));

            if (count($files) > 0) {
                array_push($output, ...$files);
            }
        }

        $output = array_merge([$compose], $output);

        return $output;
    }

    public function build(string $name, array $args = []): array
    {
        $compose = $this->generateDockerCompose($name, $args);
        $files = $this->generateFiles($name, $args['files'] ?? []);

        if (count($this->warnings) > 0) {
            return [];
        }

        return array_merge([$compose], $files);
    }

    public function getWarnings(): array
    {
        return $this->warnings ?? [];
    }

    private function mergeCompose(array $old, array $new): array
    {
        $oldContent = Yaml::parse($old['content'] ?? '') ?? [];
        $newContent = Yaml::parse($new['content'] ?? '') ?? [];

        $keys = array_merge(array_keys($oldContent), array_keys($newContent));

        $output = [];
        foreach ($keys as $key) {
            if (is_array($newContent[$key])) {
                $output[$key] = array_merge($oldContent[$key] ?? [], $newContent[$key] ?? []);

                continue;
            }

            $output[$key] = $oldContent[$key] ?? $newContent[$key];
        }

        $old['name'] = $old['name'] ?? $new['name'] ?? '';
        $old['content'] = Yaml::dump($output, 5);

        return $old;
    }

    private function generateDockerCompose(string $name, array $args): array
    {
        if (!isset($args['id'])) {
            $args['id'] = $name;
        }

        $container = [];
        $container = $this->resolveImage($container, $name, $args['version'] ?? '');
        $container = $this->resolvePorts($container, $args['ports'] ?? []);
        $container = $this->resolveVolumes($container, $args['volumes'] ?? []);
        $container = $this->resolveEnvironment($container, $args['environments'] ?? []);

        $output = ['version' => '3', 'services' => []];
        $output['services'][$args['id']] = $container;

        return [
            'name' => 'docker-compose.yml',
            'content' => Yaml::dump($output, 5)
        ];
    }

    private function resolveImage(array $container, string $name, string $version): array
    {
        try {
            $manifest = $this->container->getManifest($name);
        } catch (FileNotFoundException $ex) {
            $this->warnings[] = $ex->getMessage();

            return $container;
        }

        if (!isset($manifest['docker'])) {
            return $container;
        }

        if (!empty($manifest['docker']['build'])) {
            $container['build'] = $manifest['docker']['build'];

            return $container;
        }

        $container['image'] = $manifest['docker']['image'] ?? '';

        $tag = 'latest';
        if (!empty($manifest['docker']['tag'])) {
            $tag = $manifest['docker']['tag'];
        }

        if (!empty($version)) {
            $tag = $version;
        }

        $container['image'] .= ':' . $tag;

        return $container;
    }

    private function resolvePorts(array $container, array $ports): array
    {
        $container['ports'] = $this->validateData($ports, new Port());

        return $container;
    }

    private function resolveVolumes(array $container, array $volumes): array
    {
        $container['volumes'] = $this->validateData($volumes, new Volume());

        return $container;
    }

    private function resolveEnvironment(array $container, array $environments): array
    {
        $container['environments'] = $this->validateData($environments, new Environment());

        return $container;
    }

    private function validateData(array $data, Constraint $constraint): array
    {
        $config = [];
        foreach ($data as $value) {
            $errors = $this->validator->validate($value, $constraint);
            foreach ($errors as $error) {
                $this->warnings[] = $error->getMessage();
            }

            if (count($errors) > 0) {
                continue;
            }

            $config[] = $value;
        }

        return $config;
    }

    private function generateFiles(string $name, array $args): array
    {
        try {
            $manifest = $this->container->getManifest($name);
        } catch (FileNotFoundException $ex) {
            $this->warnings[] = $ex->getMessage();

            return [];
        }

        if (!isset($manifest['files'])) {
            return [];
        }

        $resolvers = $this->container->getResolvers($name);
        $resolverClassname = 'IsolatedResolver_' . uniqid();
        $resolverFullClassname = '\App\Builder\Tmp\\' . $resolverClassname;

        eval("namespace App\Builder\Tmp; class $resolverClassname { $resolvers }");

        $files = [];
        foreach ($manifest['files'] as $file) {
            try {
                $content = $this->github->getFile($this->container->getPath($name) . 'sources/' . $file);
            } catch (FileNotFoundException $ex) {
                $this->warnings[] = $ex->getMessage();

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
