<?php

namespace ZnCore\Domain\Repository\Traits;

use Illuminate\Support\Enumerable;
use ZnCore\Domain\Query\Entities\Query;

trait RepositoryFindAllTrait
{

    public function all(Query $query = null): Enumerable
    {
        $query = $this->forgeQuery($query);
        $queryFilter = $this->queryFilterInstance($query);
        $collection = $this->findBy($query);
        $queryFilter->loadRelations($collection);
        return $collection;
    }
}