<?php

namespace ZnCore\Domain\Repository\Interfaces;

use ZnCore\Domain\Repository\Interfaces\RepositoryInterface;
use ZnCore\Domain\Domain\Interfaces\GetEntityClassInterface;
use ZnCore\Domain\Domain\Interfaces\ReadAllInterface;

interface ReadRepositoryInterface extends
    RepositoryInterface, GetEntityClassInterface, ReadAllInterface, FindOneInterface//, RelationConfigInterface
{


}