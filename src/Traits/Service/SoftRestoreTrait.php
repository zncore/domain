<?php

namespace ZnCore\Domain\Traits\Service;

use ZnCore\Base\Enums\StatusEnum;

trait SoftRestoreTrait
{

    public function restoreById($id)
    {
        $entity = $this->oneById($id);
        $entity->restore();
        $this->getRepository()->update($entity);
        return true;
    }
}
