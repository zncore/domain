<?php

namespace ZnCore\Domain\Interfaces;

use ZnCore\Domain\Interfaces\Entity\ValidateEntityInterface;
use ZnCore\Domain\Libs\Query;

interface ForgeQueryByFilterInterface
{

    public function forgeQueryByFilter(ValidateEntityInterface $filterModel, Query $query = null);
}