<?php

namespace SymfonyEs\Bundle\PersistenceBundle\EventListener;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Doctrine\ORM\Events;
use SymfonyEs\Bundle\PersistenceBundle\Dto\UpdatedEntityDto;
use SymfonyEs\Bundle\PersistenceBundle\Util\EntityHelper;

#[AsDoctrineListener(event: Events::postPersist)]
#[AsDoctrineListener(event: Events::postUpdate)]
#[AsDoctrineListener(event: Events::preRemove)]
#[AsDoctrineListener(event: Events::postFlush)]
class DoctrineSubscriber
{
    protected array $entitiesDto = [];

    public function __construct(private readonly EntityHelper $entityHelper)
    {
    }

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
        $entity = $args->getObject();
        $className = $this->entityHelper->getRealClass($entity::class);

        $identifierValue = $this->entityHelper->getIdentifierValue($entity);
        $changedFields = [$this->entityHelper->getIdentifierName($className)];
        if ($args instanceof PostUpdateEventArgs) {
            $changedFields = array_keys($args->getObjectManager()->getUnitOfWork()->getEntityChangeSet($entity));
        }

        $this->entitiesDto[$className.'_'.$identifierValue] = new UpdatedEntityDto($className, $identifierValue, $type, $changedFields);
    }

    public function doFlush(): void
    {
    }
}
