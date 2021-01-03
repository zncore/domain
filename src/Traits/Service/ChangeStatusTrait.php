<?php

namespace ZnCore\Domain\Traits\Service;

use ZnCore\Base\Enums\StatusEnum;

trait ChangeStatusTrait
{

    public function changeStatusById(int $id, int $statusId)
    {
        $entity = $this->oneById($id);
        $entity->setStatusId($statusId);
        $this->repository->update($entity);
        return true;
    }
}
