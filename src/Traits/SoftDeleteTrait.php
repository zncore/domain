<?php

namespace ZnCore\Domain\Traits;

use ZnCore\Base\Enums\StatusEnum;

trait SoftDeleteTrait
{

    public function deleteById($id)
    {
        $entity = $this->oneById($id);
        $entity->setStatusId(StatusEnum::DELETED);
        $this->repository->update($entity);
        return true;
    }
}
