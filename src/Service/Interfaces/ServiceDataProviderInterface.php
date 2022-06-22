<?php

namespace ZnCore\Domain\Service\Interfaces;

use ZnCore\Domain\DataProvider\Libs\DataProvider;
use ZnCore\Domain\Query\Entities\Query;

interface ServiceDataProviderInterface
{

    public function getDataProvider(Query $query = null): DataProvider;

}