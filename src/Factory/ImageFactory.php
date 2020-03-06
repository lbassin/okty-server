<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Image;
use App\Entity\Image\RepositoryImage;
use App\Repository\TemplateRepositoryInterface;

class ImageFactory
{
    private $templateRepository;

    public function __construct(TemplateRepositoryInterface $templateRepository)
    {
        $this->templateRepository = $templateRepository;
    }

    public function create(string $template, string $tag): Image
    {
        $templateConfig = $this->templateRepository->getOne($template);
        // Todo add getDockerImageFromTemplate($template)
        // Todo check from template if a dockerfile is provided and use Image\Build

        return $templateConfig->getImage();
    }
}
