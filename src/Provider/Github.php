<?php declare(strict_types=1);

namespace App\Provider;

use App\Exception\BadCredentialsException;
use App\Exception\FileNotFoundException;
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
    private $cacheItemPool;

    public function __construct(
        Client $client,
        CacheItemPoolInterface $cacheItemPool,
        string $githubUser,
        string $githubRepo,
        string $githubBranch
    ) {
        $this->client = $client;
        $this->cacheItemPool = $cacheItemPool;
        $this->githubUser = $githubUser;
        $this->githubRepo = $githubRepo;
        $this->githubBranch = $githubBranch;

        $this->client->addCache($cacheItemPool);
    }

    private function getRepo(): Repo
    {
        /** @var Repo $repo */
        $repo = $this->client->api('repo');

        return $repo;
    }

    /**
     * @throws BadCredentialsException
     * @throws FileNotFoundException
     */
    public function getFile(string $path): string
    {
        try {
            return $this->getRepo()->contents()->download($this->githubUser, $this->githubRepo, $path, $this->githubBranch);
        } catch (\RuntimeException $exception) {
            if ($exception->getCode() == 401 || $exception->getCode() == 403) {
                throw new BadCredentialsException('Github API');
            }

            if ($exception->getCode() == 404) {
                throw new FileNotFoundException($path);
            }

            throw $exception;
        } catch (\ErrorException $exception) {
            throw new FileNotFoundException($path);
        }
    }

    /**
     * @throws BadCredentialsException
     */
    public function getTree(string $path): array
    {
        try {
            return $this->getRepo()->contents()->show($this->githubUser, $this->githubRepo, $path, $this->githubBranch);
        } catch (\RuntimeException $exception) {
            if ($exception->getCode() == 401 || $exception->getCode() == 403) {
                throw new BadCredentialsException('Github API');
            }

            if ($exception->getCode() == 404) {
                throw new FileNotFoundException($path);
            }

            throw $exception;
        }
    }
}
