<?php declare(strict_types=1);

namespace App\Provider\Config;

use Github\Api\Repo;
use Github\Client;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Yaml\Yaml;

class Github implements ConfigProvider
{
    private $client;
    private $githubUser;
    private $githubRepo;
    private $githubBranch;
    private $containersPath;
    private $templatesPath;
    private $cache;

    public function __construct(
        Client $client,
        string $githubUser,
        string $githubRepo,
        string $githubBranch,
        string $containersPath,
        string $templatesPath,
        CacheItemPoolInterface $cache
    )
    {
        $this->client = $client;
        $this->githubUser = $githubUser;
        $this->githubRepo = $githubRepo;
        $this->githubBranch = $githubBranch;
        $this->containersPath = $containersPath;
        $this->templatesPath = $templatesPath;
        $this->cache = $cache;
    }

    public function getAllContainers(): array
    {
        /** @var array $containers */
        $containers = [];
        /** @var array $list */
        $list = $this->getTree($this->containersPath);

        foreach ($list as $data) {
            if (!isset($data['name'])) {
                continue;
            }

            try {
                $containers[] = $this->getContainer($data['name']);
            } catch (InvalidArgumentException $exception) {
                continue;
            }
        }

        return $containers;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getContainer(string $name): array
    {
        /** @var CacheItemInterface $cacheEntry */
        $cacheEntry = $this->cache->getItem(md5($name));
        if ($cacheEntry->isHit()) {
            return $cacheEntry->get();
        }

        /** @var Repo $repo */
        $repo = $this->client->api('repo');
        $path = $this->containersPath . '/' . $name;

        /** @var array $data */
        $data = $repo->contents()->show($this->githubUser, $this->githubRepo, $path, $this->githubBranch);
        $content = base64_decode($data['content'] ?? '');

        $container = Yaml::parse($content, Yaml::PARSE_OBJECT);
        $this->cache->save($cacheEntry->set($container)->expiresAfter(3600));

        return $container;
    }

    public function getAllTemplates(): array
    {
        /** @var array $templates */
        $templates = [];
        /** @var array $list */
        $list = $this->getTree($this->templatesPath);

        foreach ($list as $data) {
            if (!isset($data['name'])) {
                continue;
            }

            $templates[] = $this->getTemplate($data['name']);
        }

        return $templates;
    }

    public function getTemplate(string $name): array
    {
        /** @var Repo $repo */
        $repo = $this->client->api('repo');
        $path = $this->templatesPath . '/' . $name;

        /** @var array $data */
        $data = $repo->contents()->show($this->githubUser, $this->githubRepo, $path, $this->githubBranch);
        $content = base64_decode($data['content'] ?? '');

        return Yaml::parse($content, Yaml::PARSE_OBJECT);
    }

    private function getTree(string $path): array
    {
        /** @var Repo $repo */
        $repo = $this->client->api('repo');
        /** @var array $fileInfo */
        $fileInfo = $repo->contents()->show($this->githubUser, $this->githubRepo, $path, $this->githubBranch);

        return $fileInfo;
    }
}
