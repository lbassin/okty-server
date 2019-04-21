<?php

namespace App\DataFixtures;

use App\Entity\Chapter;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;

class ChaptersFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $data = [
            ['id' => Uuid::uuid4()->toString(), 'name' => 'Introduction', 'position' => 1],
            ['id' => Uuid::uuid4()->toString(), 'name' => 'Installation', 'position' => 2],
        ];

        foreach ($data as $chapterData) {
            $chapter = new Chapter($chapterData['id'], $chapterData['name'], $chapterData['position']);
            $manager->persist($chapter);
        }

        $manager->flush();
    }
}
