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
        return $this->getAllElements($this->containersPath);
    }

    public function getAllTemplates(): array
    {
        return $this->getAllElements($this->templatesPath);
    }

    private function getAllElements($path): array
    {
        /** @var array $elements */
        $elements = [];
        /** @var array $list */
        $list = $this->getTree($path);

        foreach ($list as $data) {
            try {
                $elements[] = $this->getElement($path, $data['name']);
            } catch (InvalidArgumentException $e) {
                continue;
            }
        }

        return $elements;
    }

    /**
     * @throws InvalidArgumentException
     */
    private function getElement($path, $name): array
    {
        $file = $path . '/' . $name;

        /** @var CacheItemInterface $cacheEntry */
        $cacheEntry = $this->cache->getItem(md5($file));
        if ($cacheEntry->isHit()) {
            return $cacheEntry->get();
        }

        /** @var Repo $repo */
        $repo = $this->client->api('repo');

        /** @var array $data */
        $data = $repo->contents()->show($this->githubUser, $this->githubRepo, $file, $this->githubBranch);
        $content = base64_decode($data['content'] ?? '');

        /** @var array $element */
        $element = Yaml::parse($content, Yaml::PARSE_OBJECT);
        $this->cache->save($cacheEntry->set($element)->expiresAfter(3600));

        return $element;
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
