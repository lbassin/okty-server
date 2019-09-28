<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

trait DisableIdGenerator
{
    private function disableIdGenerator(string $class, ObjectManager $manager): void
    {
        /** @var ClassMetadataInfo $metadata */
        $metadata = $manager->getClassMetadata($class);

        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
        $metadata->setIdGenerator(new AssignedGenerator());
    }
}
