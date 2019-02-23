<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Template;
use App\Provider\Github;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class TemplateRepository implements TemplateRepositoryInterface
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
        $list = $this->github->getTree('templates');

        $elements = [];
        foreach ($list as $data) {
            $elements[] = $this->findOneById(substr($data['name'], 0, -4));
        }

        return $elements;
    }

    public function findOneById(string $id): Template
    {
        $file = "templates/${id}.yml";
        $content = $this->github->getFile($file);

        $data = $this->decoder->decode($content, 'yaml');
        $data['id'] = $id;

        /** @var Template $template */
        $template = $this->denormalizer->denormalize($data, Template::class, 'yaml');

        return $template;
    }
}
