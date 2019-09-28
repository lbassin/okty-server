<?php

namespace App\DataFixtures;

use App\Entity\Learning\Chapter;
use App\Entity\Learning\Lesson;
use App\Entity\Learning\Translation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Faker\Generator;
use Ramsey\Uuid\Uuid;

class LessonsFixtures extends Fixture implements DependentFixtureInterface
{

    use DisableIdGenerator;

    public function load(ObjectManager $manager)
    {
        /** @var Generator[] $faker */
        $faker = ['fr_FR' => Faker::create('fr_FR'), 'en_US' => Faker::create('en_US')];

        $this->disableIdGenerator(Lesson::class, $manager);

        $chapterRepository = $manager->getRepository(Chapter::class);
        $chapters = $chapterRepository->findBy([], ['position' => 'ASC']);

        /** @var Chapter $chapter */
        foreach ($chapters as $chapter) {
            for ($i = 0; $i <= 4; $i++) {
                $id = Uuid::uuid4()->toString();

                $lesson = new Lesson($id, "$id.name", $i, $chapter);
                $manager->persist($lesson);

                foreach (['fr_FR', 'en_US'] as $locale) {
                    $translation = new Translation(
                        Uuid::uuid4()->toString(),
                        $locale,
                        "$id.name",
                        $faker[$locale]->text(30)
                    );

                    $manager->persist($translation);
                }
            }

            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return [
            ChaptersFixtures::class,
        ];
    }
}
