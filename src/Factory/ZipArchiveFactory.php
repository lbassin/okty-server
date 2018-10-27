<?php declare(strict_types=1);

namespace App\Factory;

use ZipArchive;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class ZipArchiveFactory
{

    public static function createService(): ZipArchive
    {
        return new ZipArchive();
    }
}
