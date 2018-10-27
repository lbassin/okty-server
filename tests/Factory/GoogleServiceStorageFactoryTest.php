<?php

namespace App\Tests\Factory;

use App\Factory\GoogleServiceStorageFactory;
use PHPUnit\Framework\TestCase;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class GoogleServiceStorageFactoryTest extends TestCase
{
    private $fakeCredentials;

    protected function setUp()
    {
        $this->fakeCredentials = json_decode('{
          "type": "service_account",
          "project_id": "",
          "private_key_id": "",
          "private_key": "",
          "client_email": "",
          "client_id": "",
          "auth_uri": "",
          "token_uri": "",
          "auth_provider_x509_cert_url": "",
          "client_x509_cert_url": ""
        }', true);
    }

    public function testBuild()
    {
        $googleService = GoogleServiceStorageFactory::createService($this->fakeCredentials);

        $this->assertInstanceOf(\Google_Service_Storage::class, $googleService);
    }

    public function testScope()
    {
        $googleService = GoogleServiceStorageFactory::createService($this->fakeCredentials);

        $hasCloudScope = in_array(
            \Google_Service_Storage::CLOUD_PLATFORM,
            $googleService->getClient()->getScopes()
        );
        $this->assertTrue($hasCloudScope);
    }

    public function testCredentials()
    {
        $this->expectException(\RuntimeException::class);
        GoogleServiceStorageFactory::createService([]);
    }
}
