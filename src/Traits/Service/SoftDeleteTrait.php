<?php

namespace ZnCore\Domain\Traits\Service;

use ZnCore\Base\Enums\StatusEnum;

trait SoftDeleteTrait
{

    public function deleteById($id)
    {
        $entity = $this->oneById($id);
        $entity->delete();
        $this->repository->update($entity);
        return true;
    }
}
