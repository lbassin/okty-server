<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Container;
use App\Service\Github;
use App\ValueObject\Container\Manifest;
use App\ValueObject\File;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class ContainerRepository implements ContainerRepositoryInterface
{
    private $github;
    private $decoder;
    private $denormalizer;
    private $serializer;

    public function __construct(
        Github $github,
        DecoderInterface $decoder,
        DenormalizerInterface $denormalizer,
        SerializerInterface $serializer
    ) {
        $this->github = $github;
        $this->decoder = $decoder;
        $this->denormalizer = $denormalizer;
        $this->serializer = $serializer;
    }

    public function findAll(): array
    {
        $elements = [];

        $list = $this->github->getTree('containers');
        foreach ($list as $data) {
            $elements[] = $this->findOneById($data['name']);
        }

        return $elements;
    }

    public function findOneById(string $id): Container
    {
        $file = "containers/${id}/form.yml";

        $content = $this->github->getFile($file);

        $data = $this->decoder->decode($content, 'yaml');
        $data['id'] = $id;

        /** @var Container $container */
        $container = $this->denormalizer->denormalize($data, Container::class, 'yaml');

        return $container;
    }

    public function findManifestByContainerId(string $id): Manifest
    {
        $file = "containers/${id}/manifest.yml";

        $content = $this->github->getFile($file);

        /** @var Manifest $element */
        $element = $this->serializer->deserialize($content, Manifest::class, 'yaml');

        return $element;
    }

    public function findSource(string $containerId, string $filename): File
    {
        $content = $this->github->getFile("containers/${containerId}/sources/${filename}");

        return new File($filename, $content);
    }
}
