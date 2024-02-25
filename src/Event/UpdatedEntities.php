<?php

namespace Fico7489\PersistenceBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class UpdatedEntities extends Event
{
    public function __construct(
        private readonly array $entities,
    ) {
    }

    public function getEntities(): array
    {
        return $this->entities;
    }
}
