<?php

namespace App\Repository;

use App\Builder\ValueObject\ContainerArgs;
use App\Entity\History;
use App\Entity\HistoryContainer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class HistoryContainerRepository implements HistoryContainerRepositoryInterface
{
    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(HistoryContainer::class);
    }

    public function createFromArgs(History $history, ContainerArgs $args): HistoryContainer
    {
        $serializer = new Serializer([new JsonSerializableNormalizer()], [new JsonEncoder()]);

        return new HistoryContainer(
            $history,
            $args->getImage(),
            $serializer->serialize($args, 'json')
        );
    }
}
