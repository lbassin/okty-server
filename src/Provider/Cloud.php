<?php declare(strict_types=1);

namespace App\Provider;

use Gaufrette\File;
use Gaufrette\Filesystem;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Cloud
{
    private $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function upload($name, $path): string
    {
        $data = $this->filesystem->write($name, file_get_contents($path), true);
        dump($data);

        /** @var File $data */
        $file = $this->filesystem->get($name);
        dump($file);

        return 'https://storage.cloud.google.com/okty-7e60c.appspot.com/' . $file->getName();
    }
}
