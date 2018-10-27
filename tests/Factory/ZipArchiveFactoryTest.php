<?php

namespace App\Tests\Factory;

use App\Factory\ZipArchiveFactory;
use PHPUnit\Framework\TestCase;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class ZipArchiveFactoryTest extends TestCase
{
    public function testBuild()
    {
        $zipArchive = ZipArchiveFactory::createService();

        $this->assertInstanceOf(\ZipArchive::class, $zipArchive);
    }
}
