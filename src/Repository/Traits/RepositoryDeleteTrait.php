<?php

namespace ZnCore\Domain\Repository\Traits;

use ZnCore\Domain\Domain\Enums\EventEnum;

trait RepositoryDeleteTrait
{

    abstract protected function deleteByIdQuery($id): void;

    public function deleteById($id)
    {
        $entity = $this->oneById($id);
        $event = $this->dispatchEntityEvent($entity, EventEnum::BEFORE_DELETE_ENTITY);
        if (!$event->isSkipHandle()) {
            $this->deleteByIdQuery($id);
        }
        $event = $this->dispatchEntityEvent($entity, EventEnum::AFTER_DELETE_ENTITY);
    }
}
