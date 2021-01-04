<?php

namespace ZnCore\Domain\Base\Repositories;

use ZnCore\Domain\Helpers\FilterHelper;
use ZnCore\Domain\Interfaces\Entity\EntityIdInterface;
use ZnCore\Domain\Interfaces\ReadAllInterface;
use ZnCore\Domain\Interfaces\Repository\ReadOneInterface;
use ZnCore\Domain\Libs\Query;

abstract class BaseArrayCrudRepository extends BaseCrudRepository implements ReadAllInterface, ReadOneInterface
{

    abstract protected function getItems(): array;

    public function all(Query $query = null)
    {
        $items = $this->getItems();
        if ($query) {
            $items = FilterHelper::filterItems($items, $query);
        }
        return $this->em->createEntityCollection($this->getEntityClass(), $items);
    }

    public function count(Query $query = null): int
    {
        $collection = $this->all($query);
        return $collection->count();
    }

    public function oneById($id, Query $query = null): EntityIdInterface
    {
        $collection = $this->all($query);
        return $collection->first();
    }
}
