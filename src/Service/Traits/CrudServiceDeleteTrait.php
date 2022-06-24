<?php

namespace ZnCore\Domain\Service\Traits;

use ZnCore\Domain\Domain\Enums\EventEnum;

trait CrudServiceDeleteTrait
{

    public function deleteById($id)
    {
        if ($this->hasEntityManager()) {
            $this->getEntityManager()->beginTransaction();
        }
        try {
            $entity = $this->getRepository()->oneById($id);
            $event = $this->dispatchEntityEvent($entity, EventEnum::BEFORE_DELETE_ENTITY);
            if (!$event->isSkipHandle()) {
                $this->getRepository()->deleteById($id);
            }
            $event = $this->dispatchEntityEvent($entity, EventEnum::AFTER_DELETE_ENTITY);
        } catch (\Throwable $e) {
            if ($this->hasEntityManager()) {
                $this->getEntityManager()->rollbackTransaction();
            }
            throw $e;
        }
        if ($this->hasEntityManager()) {
            $this->getEntityManager()->commitTransaction();
        }
    }
}
