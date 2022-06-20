<?php

namespace ZnCore\Domain\Interfaces\Service;

use ZnCore\Base\Libs\DataProvider\Libs\DataProvider;
use ZnCore\Base\Libs\Query\Entities\Query;

interface ServiceDataProviderInterface
{

    public function getDataProvider(Query $query = null): DataProvider;

}