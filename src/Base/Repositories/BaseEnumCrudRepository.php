<?php

namespace ZnCore\Domain\Base\Repositories;

use ZnCore\Base\Helpers\EnumHelper;

abstract class BaseEnumCrudRepository extends BaseArrayCrudRepository
{

    abstract public function enumClass(): string;

    protected function getItems(): array
    {
        return EnumHelper::getItems($this->enumClass());
    }
}
