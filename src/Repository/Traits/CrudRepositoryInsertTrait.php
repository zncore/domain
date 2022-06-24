<?php

namespace ZnCore\Domain\Repository\Traits;

use ZnCore\Base\Validation\Helpers\ValidationHelper;
use ZnCore\Domain\Domain\Enums\EventEnum;
use ZnCore\Domain\Entity\Interfaces\EntityIdInterface;

trait CrudRepositoryInsertTrait
{

    abstract protected function insertRaw($entity): void;

    public function create(EntityIdInterface $entity)
    {
        ValidationHelper::validateEntity($entity);
        $event = $this->dispatchEntityEvent($entity, EventEnum::BEFORE_CREATE_ENTITY);
        if ($event->isPropagationStopped()) {
            return $entity;
        }
        $this->insertRaw($entity);
        $event = $this->dispatchEntityEvent($entity, EventEnum::AFTER_CREATE_ENTITY);
    }
}
