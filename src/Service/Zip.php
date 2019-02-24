<?php declare(strict_types=1);

namespace App\Service;

use App\ValueObject\File;
use App\ValueObject\Project;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use ZipArchive;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Zip
{
    private $normalizer;

    public function __construct(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function zip(Project $project): string
    {
        $path = tempnam(sys_get_temp_dir(), 'okty');

        try {
            $zip = new ZipArchive();
            $created = $zip->open($path, ZipArchive::OVERWRITE);
            if ($created !== true) {
                throw new \RuntimeException('Output directory not writable');
            }

            $files = $this->normalizer->normalize($project);

            /** @var File $file */
            foreach ($files as $file) {
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
