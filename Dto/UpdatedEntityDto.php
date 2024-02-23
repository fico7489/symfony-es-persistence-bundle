<?php

namespace SymfonyEs\Bundle\PersistenceBundle\Dto;

class UpdatedEntityDto
{
    final public const TYPE_CREATE = 'created';
    final public const TYPE_UPDATE = 'updated';
    final public const TYPE_DELETE = 'deleted';

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
