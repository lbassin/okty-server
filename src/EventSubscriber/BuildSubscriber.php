<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\ValueObject\Service\Args;
use App\Entity\History;
use App\Entity\User;
use App\Event\Build\AddContainerEvent;
use App\Event\Build\AfterBuildEvent;
use App\Event\Build\BeforeBuildEvent;
use App\Event\Build\BuildEvent;
use App\Repository\HistoryContainerRepositoryInterface;
use App\Repository\HistoryRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class BuildSubscriber implements EventSubscriberInterface
{
    private $tokenStorage;
    private $historyContainerRepository;
    private $historyRepository;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        HistoryContainerRepositoryInterface $historyContainerRepository,
        HistoryRepositoryInterface $historyRepository
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->historyContainerRepository = $historyContainerRepository;
        $this->historyRepository = $historyRepository;
    }

    public static function getSubscribedEvents()
    {
        return [
            BuildEvent::BEFORE_BUILD => 'beforeBuild',
            BuildEvent::ADD_CONTAINER => 'addContainer',
            BuildEvent::AFTER_BUILD => 'afterBuild',
        ];
    }

    public function beforeBuild(BeforeBuildEvent $event): void
    {

    }

    public function addContainer(AddContainerEvent $event): void
    {

    }

    public function afterBuild(AfterBuildEvent $event): void
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();
        if (!$user || $user == 'anon.') {
            return;
        }

        $history = new History($user);
        foreach ($event->getContainers() as $container) {
            $args = new Args($container);
            $historyContainer = $this->historyContainerRepository->createFromArgs($history, $args);

            $history->addContainer($historyContainer);
        }
        $this->historyRepository->save($history);
    }
}
