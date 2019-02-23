<?php

namespace App\Repository;

use App\Builder\ValueObject\ContainerArgs;
use App\Entity\History;
use App\Entity\HistoryContainer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class HistoryContainerRepository implements HistoryContainerRepositoryInterface
{
    private $entityManager;
    private $repository;
    private $serializer;

    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(HistoryContainer::class);
        $this->serializer = $serializer;
    }

    public function createFromArgs(History $history, ContainerArgs $args): HistoryContainer
    {
        return new HistoryContainer(
            $history,
            $args->getImage(),
            $this->serializer->serialize($args, 'json')
        );
    }
}
