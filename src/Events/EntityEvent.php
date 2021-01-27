<?php

namespace ZnCore\Domain\Events;

use Symfony\Contracts\EventDispatcher\Event;
use ZnCore\Domain\Interfaces\Entity\EntityIdInterface;

class EntityEvent extends Event
{

    private $entity;

    public function __construct(EntityIdInterface $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity(): EntityIdInterface
    {
        return $this->entity;
    }
}
