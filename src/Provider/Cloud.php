<?php declare(strict_types=1);

namespace App\Provider;

use Gaufrette\Adapter\GoogleCloudStorage;
use Gaufrette\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Cloud
{
    private $filesystem;
    private $urlPrefix;

    public function __construct(Filesystem $filesystem, string $urlPrefix)
    {
        $this->filesystem = $filesystem;
        $this->urlPrefix = $urlPrefix;
    }

    /**
     * @throws AccessDeniedException
     */
    public function upload($path): string
    {
        /** @var GoogleCloudStorage $adapter */
        $adapter = $this->filesystem->getAdapter();

        $name = uniqid('okty-');
        try {
            $this->filesystem->write($name, file_get_contents($path), true);
        } catch (\Exception $ex) {
            throw new AccessDeniedException($ex->getMessage());
        }

        return sprintf('%s/%s/%s/%s',
            $this->urlPrefix, $adapter->getBucket(), $adapter->getOptions()['directory'] ?? '', $name);
    }
}