<?php declare(strict_types=1);

namespace App\Factory;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class GoogleCloudStorageFactory
{

    public static function createService()
    {
        $client = new \Google_Client();
        $client->addScope(\Google_Service_Storage::CLOUD_PLATFORM);
        $client->useApplicationDefaultCredentials();

        return new \Google_Service_Storage($client);
    }

}
