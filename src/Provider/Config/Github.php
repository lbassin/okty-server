<?php declare(strict_types=1);

namespace App\Provider\Config;

use Github\Api\Repo;
use Github\Client;
use Github\Exception\RuntimeException;
use GraphQL\Error\UserError;
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

    public function getContainer(string $id): array
    {
        return $this->getElement($this->containersPath, $id);
    }

    public function getAllTemplates(): array
    {
        return $this->getAllElements($this->templatesPath);
    }

    public function getTemplate(string $id): array
    {
        return $this->getElement($this->templatesPath, $id);
    }

    private function getAllElements($path): array
    {
        /** @var array $elements */
        $elements = [];
        /** @var array $list */
        $list = $this->getTree($path);

        foreach ($list as $data) {
            $elements[] = $this->getElement($path, $data['name']);
        }

        return $elements;
    }

    private function getElement($path, $name): array
    {
        $file = $path . '/' . $name;

        /** @var CacheItemInterface $cacheEntry */
        try {
            $cacheEntry = $this->cache->getItem(md5($file));
            if ($cacheEntry->isHit()) {
                return $cacheEntry->get();
            }
        } catch (InvalidArgumentException $e) {
            // Fetch data if cache failed
        }

        try {
            /** @var Repo $repo */
            $repo = $this->client->api('repo');
            $data = $repo->contents()->show($this->githubUser, $this->githubRepo, $file, $this->githubBranch);
        } catch (RuntimeException $exception) {
            throw new UserError('Element not found');
        }

        $content = base64_decode($data['content'] ?? '');
        $element = Yaml::parse($content, Yaml::PARSE_OBJECT);
        $element['id'] = pathinfo($name, PATHINFO_FILENAME);

        $this->cache->save($cacheEntry->set($element)->expiresAfter(3600));

        return $element;
    }

    private function getTree(string $path): array
    {
        try {
            /** @var Repo $repo */
            $repo = $this->client->api('repo');
            return $repo->contents()->show($this->githubUser, $this->githubRepo, $path, $this->githubBranch);
        } catch (RuntimeException $exception) {
            throw new UserError('Element not found');
        }
    }
}
