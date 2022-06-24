<?php

namespace ZnCore\Domain\Repository\Traits;

use ZnCore\Domain\Domain\Enums\EventEnum;
use ZnCore\Domain\Query\Entities\Query;
use ZnCore\Domain\Relation\Libs\QueryFilter;

trait RepositoryQueryFilterTrait
{

    protected function queryFilterInstance(Query $query = null): QueryFilter
    {
        $query = $this->forgeQuery($query);
        /** @var QueryFilter $queryFilter */
        $queryFilter = new QueryFilter($this, $query);
        return $queryFilter;
    }
}
