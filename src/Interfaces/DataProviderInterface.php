<?php

namespace ZnCore\Domain\Interfaces;

use ZnCore\Domain\Libs\DataProvider;
use ZnCore\Domain\Libs\Query;

interface DataProviderInterface
{

    public function getDataProvider(Query $query = null): DataProvider;

}