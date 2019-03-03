<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Container;
use App\ValueObject\Container\Manifest;
use App\ValueObject\File;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class ContainerDevRepository implements ContainerRepositoryInterface
{
    private $decoder;
    private $denormalizer;
    private $serializer;

    private $apiUrl;

    public function __construct(
        DecoderInterface $decoder,
        DenormalizerInterface $denormalizer,
        SerializerInterface $serializer
    ) {
        $this->decoder = $decoder;
        $this->denormalizer = $denormalizer;
        $this->serializer = $serializer;

        $this->apiUrl = 'http://config-api:3000/';
    }

    public function findAll(): array
    {
        $file = 'containers';
        $list = json_decode(file_get_contents($this->apiUrl.$file));

        $elements = [];
        foreach ($list as $name) {
            $elements[] = $this->findOneById($name);
        }

        return $elements;
    }

    public function findOneById(string $id): Container
    {
        $file = "containers/${id}/form.yml";
        $content = file_get_contents($this->apiUrl.$file);

        $data = $this->decoder->decode($content, 'yaml');
        $data['id'] = $id;

        /** @var Container $container */
        $container = $this->denormalizer->denormalize($data, Container::class, 'yaml');

        return $container;
    }

    public function findManifestByContainerId(string $id): Manifest
    {
        $file = "containers/${id}/manifest.yml";
        $content = file_get_contents($this->apiUrl.$file);

        /** @var Manifest $element */
        $element = $this->serializer->deserialize($content, Manifest::class, 'yaml');

        return $element;
    }

    public function findSource(string $containerId, string $filename): File
    {
        $file = "containers/${containerId}/sources/${filename}";
        $content = file_get_contents($this->apiUrl.$file);

        return new File($filename, $content);
    }
}
