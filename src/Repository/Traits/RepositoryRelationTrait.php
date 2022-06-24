<?php

namespace ZnCore\Domain\Repository\Traits;

use Illuminate\Support\Collection;
use ZnCore\Domain\Query\Entities\Query;
use ZnCore\Domain\Relation\Libs\QueryFilter;

trait RepositoryRelationTrait
{

    abstract protected function queryFilterInstance(Query $query = null): QueryFilter;

    public function relations() {
        return [];
    }

    public function loadRelations(Collection $collection, array $with)
    {
        $query = $this->forgeQuery();
        $query->with($with);
        $queryFilter = $this->queryFilterInstance($query);
        $queryFilter->loadRelations($collection);
    }
}
