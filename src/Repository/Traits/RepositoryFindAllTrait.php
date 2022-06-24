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
        $this->loadRelationsByQuery($collection, $query);
        return $collection;
    }
}
