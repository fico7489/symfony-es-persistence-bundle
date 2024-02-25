<?php

namespace Fico7489\PersistenceBundle\EventListener;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;
use Fico7489\PersistenceBundle\Event\UpdatedEntity;
use Psr\EventDispatcher\EventDispatcherInterface;

#[AsDoctrineListener(event: Events::postPersist)]
class DoctrineListener
{
    public function __construct(private readonly EventDispatcherInterface $eventDispatcher)
    {
    }

    public function postPersist(PostPersistEventArgs $args): void
    {
        $entity = $args->getObject();

        $this->eventDispatcher->dispatch(new UpdatedEntity($entity->getId(), $entity::class));
    }
}
