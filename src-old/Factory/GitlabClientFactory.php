<?php

declare(strict_types=1);

namespace App\Factory;

use Gitlab\Client;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class GitlabClientFactory
{
    public static function createService(): Client
    {
        return Client::create('https://gitlab.com');
    }
}
