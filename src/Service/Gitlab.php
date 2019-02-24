<?php declare(strict_types=1);

namespace App\Service;

use App\Exception\BadCredentialsException;
use Gitlab\Client as GitlabClient;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Omines\OAuth2\Client\Provider\Gitlab as GitlabOAuth;
use Psr\Log\LoggerInterface;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Gitlab
{
    private $gitlabOAuth;
    private $gitlabClient;
    private $logger;

    public function __construct(GitlabOAuth $gitlabOAuth, GitlabClient $gitlabClient, LoggerInterface $logger)
    {
        $this->gitlabOAuth = $gitlabOAuth;
        $this->gitlabClient = $gitlabClient;
        $this->logger = $logger;
    }

    public function auth(string $code, string $state): string
    {
        try {
            $accessToken = $this->gitlabOAuth->getAccessToken('authorization_code', [
                'code' => $code,
                'state' => $state,
            ]);
        } catch (IdentityProviderException $e) {
            $this->logger->warning($e->getResponseBody());
            throw new BadCredentialsException('Gitlab OAuth (Wrong auth code)');
        }

        return $accessToken->getToken();
    }

    public function getUser(string $accessToken)
    {
        $this->gitlabClient->authenticate($accessToken, GitlabClient::AUTH_OAUTH_TOKEN);

        return $this->gitlabClient->users()->me();
    }
}
