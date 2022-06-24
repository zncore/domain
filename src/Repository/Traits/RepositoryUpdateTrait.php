<?php

namespace ZnCore\Domain\Repository\Traits;

use ZnCore\Base\Validation\Helpers\ValidationHelper;
use ZnCore\Domain\Domain\Enums\EventEnum;
use ZnCore\Domain\Entity\Interfaces\EntityIdInterface;

trait RepositoryUpdateTrait
{

    abstract protected function updateQuery($id, array $data): void;

    public function update(EntityIdInterface $entity)
    {
        ValidationHelper::validateEntity($entity);
        $this->oneById($entity->getId());
        $event = $this->dispatchEntityEvent($entity, EventEnum::BEFORE_UPDATE_ENTITY);
        $data = $this->mapperEncodeEntity($entity);
        $this->updateQuery($entity->getId(), $data);
        $event = $this->dispatchEntityEvent($entity, EventEnum::AFTER_UPDATE_ENTITY);
    }
}
