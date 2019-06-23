<?php declare(strict_types=1);

namespace App\Service;

use App\Exception\BadCredentialsException;
use App\Exception\FileNotFoundException;
use App\ValueObject\File;
use App\ValueObject\Github\Author;
use App\ValueObject\Github\Commit;
use App\ValueObject\Github\Target;
use Github\Api\Repo;
use Github\Client as GithubClient;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\Github as GithubOAuth;
use Psr\Log\LoggerInterface;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Github
{
    private $githubClient;
    private $githubUser;
    private $githubRepo;
    private $githubBranch;
    private $githubOAuth;
    private $logger;
    private $cache;
    private $committerName;
    private $committerEmail;

    public function __construct(
        GithubOAuth $githubOAuth,
        GithubClient $githubClient,
        LoggerInterface $logger,
        Cache $cache,
        string $githubUser,
        string $githubRepo,
        string $githubBranch,
        string $committerName,
        string $committerEmail
    ) {
        $this->githubOAuth = $githubOAuth;
        $this->githubClient = $githubClient;
        $this->logger = $logger;
        $this->cache = $cache;
        $this->githubUser = $githubUser;
        $this->githubRepo = $githubRepo;
        $this->githubBranch = $githubBranch;
        $this->committerName = $committerName;
        $this->committerEmail = $committerEmail;
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
        if ($this->cache->has("github.$path")) {
            return (string) $this->cache->get("github.$path");
        }

        try {
            $data = $this->getRepo()
                ->contents()
                ->download($this->githubUser, $this->githubRepo, $path, $this->githubBranch);

            $this->cache->set("github.$path", $data);

            return $data ?? '';
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
        if ($this->cache->has("github.$path")) {
            return $this->cache->get("github.$path");
        }

        try {
            $data = $this->getRepo()
                ->contents()
                ->show($this->githubUser, $this->githubRepo, $path, $this->githubBranch);

            $this->cache->set("github.$path", $data);

            return $data;
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
            $this->logger->warning($e->getResponseBody());
            throw new BadCredentialsException('Github OAuth (Wrong auth code)');
        }

        return $accessToken->getToken();
    }

    public function getUser(string $accessToken): array
    {
        $this->githubClient->authenticate($accessToken, null, GithubClient::AUTH_URL_TOKEN);

        return $this->githubClient->me()->show();
    }

    public function getLastCommit(string $branch): Commit
    {
        $data = $this->githubClient->repos()
            ->branches($this->githubUser, $this->githubRepo, $branch);

        return new Commit($data);
    }

    public function sendFile(File $file): string
    {
        $params = [
            'content' => base64_encode($file->getContent()),
            'encoding' => 'base64',
        ];

        $response = $this->githubClient->gitData()->blobs()
            ->create($this->githubUser, $this->githubRepo, $params);

        return $response['sha'];
    }

    public function createTree(string $baseTree, array $uploaded, Target $target): string
    {
        $files = [];
        foreach ($uploaded as $file) {
            $files[] = [
                'path' => $target->getFolder().$file['name'],
                'mode' => '100644',
                'type' => 'blob',
                'sha' => $file['sha'],
            ];
        }

        $treeData = [
            'base_tree' => $baseTree,
            'tree' => $files,
        ];

        $response = $this->githubClient->gitData()->trees()
            ->create($this->githubUser, $this->githubRepo, $treeData);

        return $response['sha'];
    }

    public function commit(Author $author, Target $target, string $lastCommit, string $newTreeSha): void
    {
        $params = [
            'message' => $target->getCommitMessage(),
            'tree' => $newTreeSha,
            'parents' => [$lastCommit],
            'author' => [
                'name' => $author->getName(),
                'email' => $author->getEmail(),
            ],
            'committer' => [
                'name' => $this->committerName,
                'email' => $this->committerEmail,
            ],
        ];

        $commit = $this->githubClient->gitData()->commits()
            ->create($this->githubUser, $this->githubRepo, $params);

        $reference = [
            'ref' => 'refs/heads/'.$target->getBranch(),
            'sha' => $commit['sha'],
        ];

        try {
            $this->githubClient->git()->references()
                ->create($this->githubUser, $this->githubRepo, $reference);
        } catch (\Exception $exception) {
            throw new \LogicException("Branch {$target->getBranch()} already exists");
        }
    }

    public function upload(array $files, Author $author, Target $target): void
    {
        $uploaded = [];

        /** @var File $file */
        foreach ($files as $file) {
            $uploaded[] = [
                'name' => $file->getName(),
                'sha' => $this->sendFile($file),
            ];
        }

        $lastCommit = $this->getLastCommit('dev');
        $newTreeSha = $this->createTree($lastCommit->getTreeSha(), $uploaded, $target);

        $this->commit($author, $target, $lastCommit->getCommitSha(), $newTreeSha);
    }

    public function requestMerge(Target $target, string $title, string $message): string
    {
        $pullRequest = $this->githubClient->pullRequest()->create($this->githubUser, $this->githubRepo, [
            'base' => 'dev',
            'head' => $target->getBranch(),
            'title' => $title,
            'body' => $message,
        ]);

        return $pullRequest['html_url'];
    }
}
