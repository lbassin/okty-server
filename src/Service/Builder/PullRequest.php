<?php

declare(strict_types=1);

namespace App\Service\Builder;

use InvalidArgumentException;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class PullRequest
{

    public function getTitle(array $container): string
    {
        if (empty($container['image'])) {
            throw new InvalidArgumentException('Container image is required');
        }

        return "Add {$container['image']} container";
    }

    public function getMessage(array $container): string
    {
        if (empty($container['image'])) {
            throw new InvalidArgumentException('Container image is required');
        }

        $hubLink = 'https://hub.docker.com/r/';
        $name = strpos($container['image'], '/') === false ? '_/'.$container['image'] : $container['image'];

        $hubLink .= $name;

        return <<<EOL
New container {$container['image']} from https://builder.okty.io/

Here is the docker hub link : [{$hubLink}]({$hubLink})
EOL;
    }

    public function requestToManifestContent(array $request): string
    {
        if (empty($request['image'])) {
            throw new InvalidArgumentException('Container Image is required');
        }

        $file = [
            'docker' => [
                'image' => $request['image'],
                'tag' => $request['tag'] ?? 'latest',
            ],
        ];

        return Yaml::dump($file, 10);
    }

    public function requestToFormContent(array $request): string
    {
        if (empty($request['name']) && empty($request['logo'])) {
            throw new InvalidArgumentException('Name and logo are required');
        }

        $config = array_map(function ($group) {
            unset($group['editing']);

            return $group;
        }, $request['config']);

        $file = [
            'name' => $request['name'],
            'logo' => $request['logo'],
            'config' => $config,
        ];

        return Yaml::dump($file, 10);
    }
}
