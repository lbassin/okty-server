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
            $created = $zip->open($path, ZipArchive::OVERWRITE);
            if ($created !== true) {
                throw new \RuntimeException('Output directory not writable');
            }

            foreach ($files as $file) {
                if (!isset($file['name']) || empty($file['content'])) {
                    continue;
                }

                $added = $zip->addFromString($file['name'], $file['content']);
                if ($added !== true) {
                    throw new \RuntimeException("Cannot add file ${$file['name']} inside zip");
                }
            }

            if ($zip->close() !== true) {
                throw new \RuntimeException('Cannot save file');
            }

            if (!is_file($path)) {
                throw new \RuntimeException('Zip generation failed');
            }

            return $path;
        } catch (\Exception $exception) {
            $error = 'An error occured while generating zip file';
            if ($exception instanceof \RuntimeException) {
                $error = $exception->getMessage();
            }

            throw new \RuntimeException($error);
        }
    }
}
