<?php declare(strict_types=1);

namespace App\Helper;

use ZipArchive;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class ZipHelper
{
    private $zipArchive;

    public function __construct(ZipArchive $zipArchive)
    {
        $this->zipArchive = $zipArchive;
    }

    public function zip(array $files): string
    {
        $path = tempnam(sys_get_temp_dir(), 'okty');

        try {
            $zip = $this->zipArchive;
            $zip->open($path, ZipArchive::OVERWRITE);

            foreach ($files as $file) {
                if (!isset($file['name']) || empty($file['content'])) {
                    continue;
                }

                $zip->addFromString($file['name'], $file['content']);
            }

            if (!$zip->close()) {
                throw new \RuntimeException('Cannot save file');
            }

            return $path;
        } catch (\Exception $exception) {
            throw new \RuntimeException('An error occured while generating zip file');
        }
    }
}
