<?php

namespace App\DataFixtures;

use App\Entity\Learning\Chapter;
use App\Entity\Learning\Lesson;
use App\Entity\Learning\Step;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;

class StepsFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr');

        $chapterRepository = $manager->getRepository(Chapter::class);
        $chapters = $chapterRepository->findAll();

        /** @var Chapter $chapter */
        foreach ($chapters as $chapter) {
            /** @var Lesson $lesson */
            foreach ($chapter->getLessons() as $lesson) {
                $stepCount = $faker->numberBetween(2, 5);

                foreach (range(2, $stepCount) as $index) {
                    $text = $faker->realText(400);
                    $step = new Step(Uuid::uuid4()->toString(), $index - 1, $text, $lesson);
                    $manager->persist($step);
                }
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            LessonsFixtures::class,
        ];
    }
}
