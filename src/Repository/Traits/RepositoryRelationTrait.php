<?php

namespace ZnCore\Domain\Repository\Traits;

use Illuminate\Support\Collection;

trait RepositoryRelationTrait
{

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
