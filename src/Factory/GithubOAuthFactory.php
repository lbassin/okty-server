<?php

declare(strict_types=1);

namespace App\Factory;

use League\OAuth2\Client\Provider\Github;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class GithubOAuthFactory
{

    public static function createService(string $clientId, string $clientSecret): Github
    {
        return new Github([
            'clientId' => $clientId,
            'clientSecret' => $clientSecret
        ]);
    }
}
