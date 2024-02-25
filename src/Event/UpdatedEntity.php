<?php

namespace Fico7489\PersistenceBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class UpdatedEntity extends Event
{
    public function __construct(
        private readonly int $id,
        private readonly string $className,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getClassName(): string
    {
        return $this->className;
    }
}
