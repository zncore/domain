<?php

namespace ZnCore\Domain\Events;

use Symfony\Contracts\EventDispatcher\Event;
use ZnCore\Domain\Interfaces\Entity\EntityIdInterface;
use ZnCore\Domain\Traits\Event\EventSkipHandleTrait;

class EntityEvent extends Event
{

    use EventSkipHandleTrait;

    private $entity;

    public function __construct(object $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity(): object
    {
        return $this->entity;
    }
}
