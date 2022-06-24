<?php

namespace ZnCore\Domain\Service\Traits;

use ZnCore\Domain\Domain\Enums\EventEnum;
use ZnCore\Domain\Entity\Helpers\EntityHelper;

trait CrudServiceUpdateTrait
{

    public function updateById($id, $data)
    {
        if ($this->hasEntityManager()) {
            $this->getEntityManager()->beginTransaction();
        }
        try {
            $entity = $this->getRepository()->oneById($id);
            EntityHelper::setAttributes($entity, $data);
            $event = $this->dispatchEntityEvent($entity, EventEnum::BEFORE_UPDATE_ENTITY);
            $this->getRepository()->update($entity);
            $event = $this->dispatchEntityEvent($entity, EventEnum::AFTER_UPDATE_ENTITY);
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
