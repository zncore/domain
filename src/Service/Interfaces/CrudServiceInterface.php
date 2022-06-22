<?php

namespace ZnCore\Domain\Service\Interfaces;

use ZnCore\Domain\Domain\Interfaces\GetEntityClassInterface;
use ZnCore\Domain\Domain\Interfaces\ReadAllInterface;

interface CrudServiceInterface extends ServiceDataProviderInterface, ServiceInterface, GetEntityClassInterface, ReadAllInterface, FindOneInterface, ModifyInterface
{


}