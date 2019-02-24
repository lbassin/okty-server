<?php declare(strict_types=1);

namespace App\Service;

use App\Exception\BadCredentialsException;
use App\Exception\FileNotFoundException;
use Github\Api\Repo;
use Github\Client as GithubClient;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\Github as GithubOAuth;
use Psr\Cache\CacheItemPoolInterface;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Github
{
    private $githubClient;
    private $githubUser;
    private $githubRepo;
    private $githubBranch;
    private $cacheItemPool;
    private $githubOAuth;

    public function __construct(
        GithubOAuth $githubOAuth,
        GithubClient $githubClient,
        CacheItemPoolInterface $cacheItemPool,
        string $githubUser,
        string $githubRepo,
        string $githubBranch
    ) {
        $this->githubOAuth = $githubOAuth;
        $this->githubClient = $githubClient;
        $this->cacheItemPool = $cacheItemPool;
        $this->githubUser = $githubUser;
        $this->githubRepo = $githubRepo;
        $this->githubBranch = $githubBranch;

        $this->githubClient->addCache($cacheItemPool);
    }

    private function getRepo(): Repo
    {
        /** @var Repo $repo */
        $repo = $this->githubClient->api('repo');

        return $repo;
    }

    /**
     * @throws BadCredentialsException
     * @throws FileNotFoundException
     */
    public function getFile(string $path): string
    {
        try {
            return $this->getRepo()->contents()->download($this->githubUser, $this->githubRepo, $path,
                $this->githubBranch);
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

    /**
     * @throw BadCredentialsException
     */
    public function auth(string $code, string $state): string
    {
        try {
            $accessToken = $this->githubOAuth->getAccessToken('authorization_code', [
                'code' => $code,
                'state' => $state,
            ]);
        } catch (IdentityProviderException $e) {
            throw new BadCredentialsException('Github OAuth (Wrong auth code)');
        }

        return $accessToken->getToken();
    }

    public function getUser(string $accessToken): array
    {
        $this->githubClient->authenticate($accessToken, null, GithubClient::AUTH_URL_TOKEN);

        return $this->githubClient->me()->show();
    }
}
