<?php

declare(strict_types=1);

namespace App\Factory;

use Omines\OAuth2\Client\Provider\Gitlab;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class GitlabOAuthFactory
{
    public static function createService(string $clientId, string $clientSecret, string $redirectUrl): Gitlab
    {
        return new Gitlab([
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'redirectUri' => $redirectUrl,
        ]);
    }
}
