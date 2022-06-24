<?php

namespace ZnCore\Domain\Repository\Traits;

use Illuminate\Support\Enumerable;
use ZnCore\Domain\Query\Entities\Query;

trait RepositoryFindAllTrait
{

    public function all(Query $query = null): Enumerable
    {
        $query = $this->forgeQuery($query);
        $collection = $this->findBy($query);
        $this->loadRelations($collection, $query->getWith() ?: []);
//        $queryFilter = $this->queryFilterInstance($query);
//        $queryFilter->loadRelations($collection);
        return $collection;
    }
}