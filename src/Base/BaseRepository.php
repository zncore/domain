<?php

namespace ZnCore\Domain\Base;

use ZnCore\Base\Helpers\DeprecateHelper;
use ZnCore\Domain\Interfaces\GetEntityClassInterface;

DeprecateHelper::hardThrow();

abstract class BaseRepository implements GetEntityClassInterface
{

}