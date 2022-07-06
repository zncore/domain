<?php

namespace ZnCore\Domain\Domain\Traits;

use ZnCore\EventDispatcher\Traits\EventDispatcherTrait;
use ZnCore\Domain\Domain\Events\EntityEvent;
use ZnCore\Domain\Domain\Events\QueryEvent;
use ZnCore\Domain\Domain\Interfaces\GetEntityClassInterface;
use ZnCore\Query\Entities\Query;

trait DispatchEventTrait
{

    use EventDispatcherTrait;

    protected function dispatchQueryEvent(Query $query, string $eventName): QueryEvent
    {
        $event = new QueryEvent($query);
        $this->getEventDispatcher()->dispatch($event, $eventName);
        return $event;
    }

    protected function dispatchEntityEvent(object $entity, string $eventName): EntityEvent
    {
        $event = new EntityEvent($entity);
        $this->getEventDispatcher()->dispatch($event, $eventName);
        return $event;
    }
}
