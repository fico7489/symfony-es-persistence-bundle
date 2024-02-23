<?php

namespace SymfonyEs\Bundle\PersistenceBundle\EventListener;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Doctrine\ORM\Events;
use SymfonyEs\Bundle\PersistenceBundle\Dto\UpdatedEntityDto;

#[AsDoctrineListener(event: Events::postPersist)]
#[AsDoctrineListener(event: Events::postUpdate)]
#[AsDoctrineListener(event: Events::preRemove)]
#[AsDoctrineListener(event: Events::postFlush)]
class DoctrineSubscriber
{
    public function postPersist(PostPersistEventArgs $args): void
    {
        $this->addEntitiesForFlush($args, UpdatedEntityDto::TYPE_CREATE);
    }

    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $this->addEntitiesForFlush($args, UpdatedEntityDto::TYPE_UPDATE);
    }

    public function preRemove(PreRemoveEventArgs $args): void
    {
        $this->addEntitiesForFlush($args, UpdatedEntityDto::TYPE_DELETE);
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        $this->doFlush();
    }

    private function addEntitiesForFlush(PostPersistEventArgs|PostUpdateEventArgs|PreRemoveEventArgs $args, string $type): void
    {

    }

    public function doFlush(): void
    {

    }
}
