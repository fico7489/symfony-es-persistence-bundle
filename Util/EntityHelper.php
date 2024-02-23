<?php

namespace SymfonyEs\Bundle\PersistenceBundle\Util;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;

class EntityHelper
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function getRealClass(string $className): string
    {
        $className = ClassUtils::getRealClass($className);

        $className = $this->entityManager->getClassMetadata($className)->rootEntityName;

        return $className;
    }

    public function getIdentifierName($entity): mixed
    {
        if (is_object($entity)) {
            $className = get_class($entity);
        } else {
            $className = $entity;
        }

        $meta = $this->entityManager->getClassMetadata($className);

        // TODO throw exception
        $identifier = $meta->getSingleIdentifierFieldName();

        return $identifier;
    }

    public function getIdentifierValue($entity): mixed
    {
        $identifier = $this->getIdentifierName($entity);

        $getter = 'get'.ucfirst($identifier);

        // TODO throw exception if not exists getter
        return $entity->{$getter}();
    }
}
