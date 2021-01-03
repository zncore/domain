<?php

namespace ZnCore\Domain\Traits;

use ZnCore\Base\Enums\StatusEnum;

trait SoftRestoreTrait
{

    public function restoreById($id)
    {
        $entity = $this->oneById($id);
        $entity->setStatusId(StatusEnum::ENABLED);
        $this->repository->update($entity);
        return true;
    }
}
