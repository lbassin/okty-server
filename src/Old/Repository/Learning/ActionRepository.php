<?php

declare(strict_types=1);

namespace App\Repository\Learning;

use App\Entity\Learning\Action;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class ActionRepository implements ActionRepositoryInterface
{
    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Action::class);
    }

    public function findById(string $id): Action
    {
        /** @var Action $action */
        $action = $this->repository->find($id);
        if (!$action) {
            throw new EntityNotFoundException();
        }

        return $action;
    }
}
