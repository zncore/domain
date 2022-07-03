<?php

namespace ZnCore\Domain\Service\Traits;

use ZnCore\Domain\Collection\Interfaces\Enumerable;
use ZnCore\Domain\DataProvider\Libs\DataProvider;
use ZnCore\Domain\Query\Entities\Query;

trait CrudServiceFindAllTrait
{

    public function getDataProvider(Query $query = null): DataProvider
    {
        $dataProvider = new DataProvider($this, $query);
        return $dataProvider;
    }

    public function findAll(Query $query = null): Enumerable
    {
        $query = $this->forgeQuery($query);
        $collection = $this->getRepository()->findAll($query);
        return $collection;
    }

    public function count(Query $query = null): int
    {
        $query = $this->forgeQuery($query);
        return $this->getRepository()->count($query);
    }
}
