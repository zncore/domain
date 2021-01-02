<?php

namespace ZnCore\Domain\Interfaces\Service;

use ZnCore\Domain\Libs\DataProvider;
use ZnCore\Domain\Libs\Query;

interface ServiceDataProviderInterface
{

    public function getDataProvider(Query $query = null): DataProvider;

}