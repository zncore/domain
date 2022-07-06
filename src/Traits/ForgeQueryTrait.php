<?php

namespace ZnCore\Domain\Traits;

use ZnCore\Domain\Enums\EventEnum;
use ZnCore\Query\Entities\Query;

trait ForgeQueryTrait
{

    protected function forgeQuery(Query $query = null): Query
    {
        $query = Query::forge($query);
        $this->dispatchQueryEvent($query, EventEnum::BEFORE_FORGE_QUERY);
        return $query;
    }
}
