<?php

namespace App\DataFixtures;

use App\Entity\Learning\Action;
use App\Entity\Learning\Chapter;
use App\Entity\Learning\Lesson;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;

class ActionsFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $chapterRepository = $manager->getRepository(Chapter::class);
        $chapters = $chapterRepository->findAll();

        /** @var Chapter $chapter */
        foreach ($chapters as $chapter) {
            /** @var Lesson $lesson */
            foreach ($chapter->getLessons() as $lesson) {
                if ($lesson->getSteps()->count() <= 2) {
                    continue;
                }

                $action = new Action(Uuid::uuid4()->toString(), 'qcm', $this->getQcmConfig());
                $manager->persist($action);

                $lastStep = $lesson->getSteps()->last();
                $lastStep->setAction($action);
            }
        }

        $manager->flush();
    }

    private function getQcmConfig(): array
    {
        return [
            'questions' => [
                [
                    'title' => 'Which one of these color is the best ?',
                    'questions' => [
                        'Blue',
                        'Red',
                        'Green',
                    ],
                    'responses' => [
                        1,
                    ],
                ],
                [
                    'title' => 'Which of these statements are true ?',
                    'questions' => [
                        'There is snow during summer',
                        'Okty is a great application',
                        'False',
                        'Youtube is a website',
                    ],
                    'responses' => [
                        2,
                        4,
                    ],
                ],
            ],
        ];
    }

    public function getDependencies(): array
    {
        return [
            StepsFixtures::class,
        ];
    }
}
