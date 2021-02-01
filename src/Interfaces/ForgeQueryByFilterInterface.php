<?php

namespace ZnCore\Domain\Interfaces;

use ZnCore\Domain\Libs\Query;

interface ForgeQueryByFilterInterface
{

    public function forgeQueryByFilter(object $filterModel, Query $query = null);
}