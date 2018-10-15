<?php declare(strict_types=1);

namespace App\Provider;

use App\Entity\Manifest;
use Github\Exception\ErrorException;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Container
{
    private $github;

    public function __construct(Github $github)
    {
        $this->github = $github;
    }

    public function getManifest($container): Manifest
    {
        $path = '';

        $content = '';
        try {
//            $content = $this->getContent()->download($this->githubUser, $this->githubRepo, $path, $this->githubBranch);
            echo '';
        } catch (ErrorException $e) {
        }

        print_r($content);

        return new Manifest();
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
