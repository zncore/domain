<?php

namespace ZnCore\Domain\Domain\Traits;

use ZnCore\Domain\Domain\Enums\EventEnum;
use ZnCore\Domain\Query\Entities\Query;

trait ForgeQueryTrait
{

    protected function forgeQuery(Query $query = null)//: Query
    {
        $query = Query::forge($query);
        $this->dispatchQueryEvent($query, EventEnum::BEFORE_FORGE_QUERY);
        return $query;
    }
}
