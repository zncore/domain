<?php

namespace ZnCore\Domain\Interfaces\Repository;

use ZnCore\Domain\Interfaces\GetEntityClassInterface;
use ZnCore\Domain\Interfaces\ReadAllInterface;

interface CrudRepositoryInterface extends RepositoryInterface, GetEntityClassInterface, ReadAllInterface, ReadOneInterface, ModifyInterface, RelationConfigInterface
{


}