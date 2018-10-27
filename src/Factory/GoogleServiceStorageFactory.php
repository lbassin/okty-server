<?php declare(strict_types=1);

namespace App\Factory;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class GoogleServiceStorageFactory
{
    public static function createService($credentials)
    {
        $client = new \Google_Client();
        $client->addScope(\Google_Service_Storage::CLOUD_PLATFORM);

        try {
            $client->setAuthConfig($credentials);
        } catch (\Exception $e) {
            throw new \RuntimeException('Google Service Storage credentials missing');
        }

        return new \Google_Service_Storage($client);
    }
}
