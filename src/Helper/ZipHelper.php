<?php declare(strict_types=1);

namespace App\Helper;

use ZipArchive;

class ZipHelper
{
    public function zip(array $files): string
    {
        $path = tempnam(sys_get_temp_dir(), 'okty');

        $zip = new ZipArchive();
        $zip->open($path, ZipArchive::OVERWRITE);

        foreach ($files as $file) {
            $zip->addFromString($file['name'], $file['content']);
        }

        try {
            $zip->close();
        } catch (\Exception $ex) {
            return ''; // To change
        }

        return $path;
    }
}
