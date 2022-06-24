<?php

namespace ZnCore\Domain\Repository\Traits;

use Illuminate\Support\Collection;
use ZnCore\Domain\Query\Entities\Query;
use ZnCore\Domain\Relation\Libs\QueryFilter;
use ZnCore\Domain\Relation\Libs\RelationLoader;

trait RepositoryRelationTrait
{

//    abstract protected function queryFilterInstance(Query $query = null): QueryFilter;

    public function relations() {
        return [];
    }

    public function loadRelations(Collection $collection, array $with)
    {
        $query = $this->forgeQuery();
        $query->with($with);

        if (method_exists($this, 'relations')) {
            $relationLoader = new RelationLoader;
            $relationLoader->setRelations($this->relations());
            $relationLoader->setRepository($this);
            $relationLoader->loadRelations($collection, $query);
        }

//        $queryFilter = $this->queryFilterInstance($query);
//        $queryFilter->loadRelations($collection);
    }
}
