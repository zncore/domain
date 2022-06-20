<?php

namespace ZnCore\Domain\Interfaces\Filter;

use ZnCore\Base\Helpers\DeprecateHelper;

DeprecateHelper::hardThrow();

interface IgnoreAttributesInterface
{

    public function ignoreAttributesFromCondition(): array;
}