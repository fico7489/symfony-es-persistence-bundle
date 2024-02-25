<?php

namespace Fico7489\PersistenceBundle\EventListener;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Doctrine\ORM\Events;
use Fico7489\PersistenceBundle\Dto\UpdatedEntity;
use Fico7489\PersistenceBundle\Event\UpdatedEntities;
use Fico7489\PersistenceBundle\Util\EntityHelper;
use Psr\EventDispatcher\EventDispatcherInterface;

#[AsDoctrineListener(event: Events::postPersist)]
#[AsDoctrineListener(event: Events::postUpdate)]
#[AsDoctrineListener(event: Events::preRemove)]
#[AsDoctrineListener(event: Events::postFlush)]
class DoctrineListener
{
    protected array $entitiesDto = [];

    public function __construct(
        private readonly EntityHelper $entityHelper,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function postPersist(PostPersistEventArgs $args): void
    {
        $this->addEntitiesForFlush($args, UpdatedEntity::TYPE_CREATE);
    }

    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $this->addEntitiesForFlush($args, UpdatedEntity::TYPE_UPDATE);
    }

    public function preRemove(PreRemoveEventArgs $args): void
    {
        $this->addEntitiesForFlush($args, UpdatedEntity::TYPE_DELETE);
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        $this->doFlush();
    }

    public function doFlush(): void
    {
        if (count($this->entitiesDto)) {
            $this->eventDispatcher->dispatch(new UpdatedEntities($this->entitiesDto));
        }

        // reset var
        $this->entitiesDto = [];
    }

    private function addEntitiesForFlush(PostPersistEventArgs|PostUpdateEventArgs|PreRemoveEventArgs $args, string $type): void
    {
        $entity = $args->getObject();
        $className = $this->entityHelper->getRealClass($entity::class);

        $identifierValue = $this->entityHelper->getIdentifierValue($entity);
        $changedFields = [$this->entityHelper->getIdentifierName($className)];
        if ($args instanceof PostUpdateEventArgs) {
            $changedFields = array_keys($args->getObjectManager()->getUnitOfWork()->getEntityChangeSet($entity));
        }

        $this->entitiesDto[$className.'_'.$identifierValue] = new UpdatedEntity($className, $identifierValue, $type, $changedFields);
    }
}
