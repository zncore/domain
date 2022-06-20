<?php

namespace ZnCore\Domain\Base\Repositories;

use ZnCore\Base\Helpers\EnumHelper;
use ZnCore\Base\Libs\Arr\Base\BaseArrayCrudRepository;

abstract class BaseEnumCrudRepository extends BaseArrayCrudRepository
{

    abstract public function enumClass(): string;

    protected function getItems(): array
    {
        return EnumHelper::getItems($this->enumClass());
    }
}
