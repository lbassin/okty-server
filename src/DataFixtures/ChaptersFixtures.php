<?php

namespace App\DataFixtures;

use App\Entity\Learning\Chapter;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;

class ChaptersFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $data = [
            ['id' => Uuid::uuid4()->toString(), 'name' => 'Introduction', 'position' => 1, 'language' => 'fr_FR'],
            ['id' => Uuid::uuid4()->toString(), 'name' => 'Commandes principales', 'position' => 2, 'language' => 'fr_FR'],
            ['id' => Uuid::uuid4()->toString(), 'name' => 'Manipuler un container', 'position' => 3, 'language' => 'fr_FR'],
        ];

        foreach ($data as $chapterData) {
            $chapter = new Chapter(
                $chapterData['id'],
                $chapterData['name'],
                $chapterData['position'],
                $chapterData['language']
            );

            $manager->persist($chapter);
        }

        $manager->flush();
    }
}
