<?php

namespace App\DataFixtures;

use App\Entity\Learning\Chapter;
use App\Entity\Learning\Translation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Ramsey\Uuid\Uuid;

class ChaptersFixtures extends Fixture
{
    use DisableIdGenerator;

    public function load(ObjectManager $manager)
    {
        $faker = ['fr_FR' => Faker::create('fr_FR'), 'en_US' => Faker::create('en_US')];

        $this->disableIdGenerator(Chapter::class, $manager);
        $this->disableIdGenerator(Translation::class, $manager);

        for ($i = 0; $i <= 4; $i++) {
            $id = Uuid::uuid4()->toString();

            $chapter = new Chapter($id, "$id.name", $i);
            $manager->persist($chapter);

            foreach (['fr_FR', 'en_US'] as $locale) {
                $translation = new Translation(
                    Uuid::uuid4()->toString(),
                    $locale,
                    $id.'.name',
                    $faker[$locale]->realText(30)
                );
                $manager->persist($translation);
            }
        }

        $manager->flush();
    }
}
