<?php

namespace ZnCore\Domain\Repository\Traits;

use Illuminate\Support\Collection;
use ZnCore\Domain\Query\Entities\Query;
use ZnCore\Domain\Relation\Libs\RelationLoader;

trait RepositoryRelationTrait
{

    public function relations() {
        return [];
    }

    public function loadRelations(Collection $collection, array $with)
    {
//        if (method_exists($this, 'relations')) {
            $relations = $this->relations();
            if(empty($relations)) {
                return;
            }
            $query = new Query();
            $query->with($with);
            $relationLoader = new RelationLoader();
            $relationLoader->setRelations($relations);
            $relationLoader->setRepository($this);
            $relationLoader->loadRelations($collection, $query);
//        }
    }

    public function loadRelationsByQuery(Collection $collection, Query $query) {
        $this->loadRelations($collection, $query->getWith() ?: []);
    }

    /*public function loadRelations(Collection $collection, array $with)
    {
        $query = $this->forgeQuery();
        $query->with($with);
        $queryFilter = $this->queryFilterInstance($query);
        $queryFilter->loadRelations($collection);
    }*/
}
