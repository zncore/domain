<?php

namespace ZnCore\Domain\Base\Repositories;

use ZnCore\Domain\Interfaces\Repository\CrudRepositoryInterface;
use ZnCore\Domain\Traits\Repository\ArrayCrudRepositoryTrait;

abstract class BaseArrayCrudRepository extends BaseCrudRepository implements CrudRepositoryInterface
{

    use ArrayCrudRepositoryTrait;
}
