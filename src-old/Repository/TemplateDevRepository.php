<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Template;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class TemplateDevRepository implements ContainerRepositoryInterface
{
    private $decoder;
    private $denormalizer;

    private $apiUrl;

    public function __construct(DecoderInterface $decoder, DenormalizerInterface $denormalizer)
    {
        $this->decoder = $decoder;
        $this->denormalizer = $denormalizer;

        $this->apiUrl = 'http://config-api/';
    }

    public function findAll(): array
    {
        $file = 'templates';
        $list = json_decode(file_get_contents($this->apiUrl.$file));

        $elements = [];
        foreach ($list as $data) {
            $elements[] = $this->findOneById(substr($data, 0, -4));
        }

        return $elements;
    }

    public function findOneById(string $id): Template
    {
        $file = "templates/${id}.yml";
        $content = file_get_contents($this->apiUrl.$file);

        $data = $this->decoder->decode($content, 'yaml');
        $data['id'] = $id;

        /** @var Template $template */
        $template = $this->denormalizer->denormalize($data, Template::class, 'yaml');

        return $template;
    }
}
