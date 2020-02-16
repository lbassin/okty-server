<?php

namespace App\DataFixtures;

use App\Entity\Learning\Chapter;
use App\Entity\Learning\Lesson;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;

class LessonsFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $chapterRepository = $manager->getRepository(Chapter::class);
        $chapters = $chapterRepository->findBy([], ['position' => 'ASC']);

        $lessons = [
            1 => [
                'Présentation',
                'Concepts utiles',
                'Installation',
            ],
            2 => [
                'Récuperer des images',
                'Gestion des containers',
                'Debug lors d\'erreur',
            ],
            3 => [
                'Acceder au container',
                'Partage de fichiers',
                'Configuration via variables environnement',
            ],
        ];

        /** @var Chapter $chapter */
        foreach ($chapters as $chapter) {
            $index = 1;
            foreach ($lessons[$chapter->getPosition()] as $lesson) {
                $lesson = new Lesson(Uuid::uuid4()->toString(), $lesson, $index, $chapter);
                $manager->persist($lesson);

                $index++;
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ChaptersFixtures::class,
        ];
    }
}
