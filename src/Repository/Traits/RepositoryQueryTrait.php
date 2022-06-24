<?php

namespace ZnCore\Domain\Repository\Traits;

use ZnCore\Domain\Domain\Enums\EventEnum;
use ZnCore\Domain\Query\Entities\Query;
use ZnCore\Domain\Relation\Libs\QueryFilter;

trait RepositoryQueryTrait
{

    protected function forgeQuery(Query $query = null): Query
    {
        $query = Query::forge($query);
        $this->dispatchQueryEvent($query, EventEnum::BEFORE_FORGE_QUERY);
        return $query;
    }

    protected function queryFilterInstance(Query $query = null)
    {
        $query = $this->forgeQuery($query);
        /** @var QueryFilter $queryFilter */
        $queryFilter = new QueryFilter($this, $query);
        return $queryFilter;
    }
}
