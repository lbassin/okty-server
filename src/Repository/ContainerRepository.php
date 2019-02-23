<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Container;
use App\Provider\Github;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class ContainerRepository implements ContainerRepositoryInterface
{
    private $github;
    private $decoder;
    private $denormalizer;

    public function __construct(Github $github, DecoderInterface $decoder, DenormalizerInterface $denormalizer)
    {
        $this->github = $github;
        $this->decoder = $decoder;
        $this->denormalizer = $denormalizer;
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
}
