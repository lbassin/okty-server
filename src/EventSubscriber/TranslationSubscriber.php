<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Annotation\Translatable;
use App\Entity\Learning\Translation;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\HttpFoundation\RequestStack;

class TranslationSubscriber implements EventSubscriber
{
    private $request;
    private $reader;
    private $entityManager;

    public function __construct(RequestStack $request, EntityManagerInterface $entityManager)
    {
        $this->request = $request->getCurrentRequest();
        $this->reader = new AnnotationReader();
        $this->entityManager = $entityManager;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postLoad,
        ];
    }

    /**
     * @throws ReflectionException
     */
    public function postLoad(LifecycleEventArgs $event): void
    {
        if (!$this->request) {
            return; // Triggered by a fixture
        }

        $language = $this->request->query->get('lang', 'en_US');

        $entity = $event->getEntity();
        $reflection = new ReflectionClass($entity);

        $classTranslatable = $this->reader->getClassAnnotation($reflection, Translatable::class);
        if (!$classTranslatable) {
            return;
        }

        $properties = $this->getTranslatableProperties($reflection);
        $keys = $this->getTranslationKeys($entity, $properties);
        $translations = $this->getTranslations($keys, $language);

        /** @var \ReflectionProperty $property */
        foreach ($properties as $property) {
            $property->setAccessible(true);

            $key = $property->getValue($entity);
            $property->setValue($entity, $translations[$key] ?? $key);

            $property->setAccessible(false);
        }
    }

    private function getTranslationKeys($entity, array $properties): array
    {
        $keys = [];
        foreach ($properties as $property) {
            $property->setAccessible(true);
            $keys[] = $property->getValue($entity);
            $property->setAccessible(false);
        }

        return $keys;
    }

    private function getTranslatableProperties(ReflectionClass $class): array
    {
        /** @var  $properties */
        $properties = [];
        foreach ($class->getProperties() as $property) {
            $isTranslatable = $this->reader->getPropertyAnnotation($property, Translatable::class);
            if (!$isTranslatable) {
                continue;
            }

            $properties[] = $property;
        }

        return $properties;
    }

    private function getTranslations(array $keys, string $language): array
    {
        $results = $this->entityManager
            ->getRepository(Translation::class)
            ->findBy(['key' => $keys, 'locale' => $language]);

        $translations = [];
        array_walk($results, function ($result) use (&$translations) {
            /** @var Translation $result */
            $translations[$result->getKey()] = $result->getValue();
        });

        return $translations;
    }
}