<?php

namespace ZnCore\Domain\Repository\Traits;

use Illuminate\Support\Enumerable;
use ZnCore\Domain\Domain\Traits\FindAllTrait;
use ZnCore\Domain\Query\Entities\Query;

trait CrudRepositoryFindAllTrait
{

//    use FindAllTrait;
    
    public function findAll(Query $query = null): Enumerable
    {
        $query = $this->forgeQuery($query);
        $collection = $this->findBy($query);
        $this->loadRelationsByQuery($collection, $query);
        return $collection;
    }
}
