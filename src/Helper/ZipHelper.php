<?php declare(strict_types=1);

namespace App\Helper;

use App\Builder\ValueObject\Project\File;
use App\Builder\ValueObject\Project\Project;
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

    public function zip(Project $project): string
    {
        $path = tempnam(sys_get_temp_dir(), 'okty');

        try {
            $zip = $this->zipArchive;
            $created = $zip->open($path, ZipArchive::OVERWRITE);
            if ($created !== true) {
                throw new \RuntimeException('Output directory not writable');
            }

            /** @var File $file */
            foreach ($project->getFiles() as $file) {
                $added = $zip->addFromString($file->getName(), $file->getContent());
                if ($added !== true) {
                    throw new \RuntimeException(sprintf("Cannot add file %s inside zip", $file->getName()));
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
