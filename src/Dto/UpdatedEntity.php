<?php

namespace Fico7489\PersistenceBundle\Dto;

class UpdatedEntity
{
    final public const TYPE_CREATE = 'create';
    final public const TYPE_UPDATE = 'update';
    final public const TYPE_DELETE = 'delete';

    public function __construct(
        private readonly string $className,
        private readonly int $identifier,
        private readonly string $type,
        private readonly array $changedFields,
    ) {
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getIdentifier(): int
    {
        return $this->identifier;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getChangedFields(): array
    {
        return $this->changedFields;
    }
}
