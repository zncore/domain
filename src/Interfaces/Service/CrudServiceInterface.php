<?php

namespace ZnCore\Domain\Interfaces\Service;

use ZnCore\Domain\Interfaces\DataProviderInterface;
use ZnCore\Domain\Interfaces\GetEntityClassInterface;
use ZnCore\Domain\Interfaces\ReadAllInterface;

interface CrudServiceInterface extends DataProviderInterface, ServiceInterface, GetEntityClassInterface, ReadAllInterface, ReadOneInterface, ModifyInterface
{


}