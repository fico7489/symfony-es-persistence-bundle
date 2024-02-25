<?php

namespace Fico7489\PersistenceBundle\Util;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;

class EntityHelper
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
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

    public function refresh(mixed $entity): mixed
    {
        if (!$entity) {
            return null;
        }

        $className = $this->getRealClass($entity::class);

        $identifierName = $this->getIdentifierName($entity);
        $identifierValue = $this->getIdentifierValue($entity);

        $repository = $this->entityManager->getRepository($className);
        $entity = $repository->findOneBy([$identifierName => $identifierValue]);

        if ($entity) {
            $this->entityManager->refresh($entity);

            if ($entity) {
                return $entity;
            }
        }

        return null;
    }

    public function refreshByClassNameId(string $className, int $identifierValue): mixed
    {
        $className = $this->getRealClass($className);

        $repository = $this->entityManager->getRepository($className);

        $identifierName = $this->getIdentifierName($className);

        $entity = $repository->findOneBy([$identifierName => $identifierValue]);

        if ($entity) {
            $this->entityManager->refresh($entity);

            if ($entity) {
                return $entity;
            }
        }

        return null;
    }

    public function getRealClass(string $className): string
    {
        $className = ClassUtils::getRealClass($className);
        $className = $this->entityManager->getClassMetadata($className)->rootEntityName;

        return $className;
    }
}
