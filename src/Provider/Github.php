<?php declare(strict_types=1);

namespace App\Provider;

use Github\Api\Repo;
use Github\Client;
use Psr\Cache\CacheItemPoolInterface;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Github
{
    private $client;
    private $githubUser;
    private $githubRepo;
    private $githubBranch;
    private $containersPath;
    private $templatesPath;
    private $cacheItemPool;

    public function __construct(
        Client $client,
        string $githubUser,
        string $githubRepo,
        string $githubBranch,
        string $containersPath,
        string $templatesPath,
        CacheItemPoolInterface $cacheItemPool
    )
    {
        $this->client = $client;
        $this->githubUser = $githubUser;
        $this->githubRepo = $githubRepo;
        $this->githubBranch = $githubBranch;
        $this->containersPath = $containersPath;
        $this->templatesPath = $templatesPath;
        $this->cacheItemPool = $cacheItemPool;

        $this->client->addCache($cacheItemPool);
    }

    private function getRepo(): Repo
    {
        /** @var Repo $repo */
        $repo = $this->client->api('repo');

        return $repo;
    }

    public function getFile(string $path): string
    {
        return $this->getRepo()->contents()->download($this->githubUser, $this->githubRepo, $path, $this->githubBranch);
    }

    public function getTree(string $path): array
    {
        return $this->getRepo()->contents()->show($this->githubUser, $this->githubRepo, $path, $this->githubBranch);
    }

    public function download(string $path)
    {
        return $this->getRepo()->contents()->download($this->githubUser, $this->githubRepo, $path, $this->githubBranch);
    }
}
