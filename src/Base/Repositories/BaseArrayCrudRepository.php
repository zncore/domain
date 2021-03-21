<?php

namespace ZnCore\Domain\Base\Repositories;

use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnCore\Domain\Helpers\FilterHelper;
use ZnCore\Domain\Interfaces\Entity\EntityIdInterface;
use ZnCore\Domain\Interfaces\ReadAllInterface;
use ZnCore\Domain\Interfaces\Repository\ReadOneInterface;
use ZnCore\Domain\Libs\Query;

abstract class BaseArrayCrudRepository extends BaseCrudRepository implements ReadAllInterface, ReadOneInterface
{

    abstract protected function getItems(): array;
    abstract protected function setItems(array $items);

    public function all(Query $query = null)
    {
        $items = $this->getItems();
        if ($query) {
            $items = FilterHelper::filterItems($items, $query);
        }
        return $this->getEntityManager()->createEntityCollection($this->getEntityClass(), $items);
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

    public function create(EntityIdInterface $entity)
    {
        $items = $this->getItems();

        $item = EntityHelper::toArray($entity);
        $items[] = $item;

        $this->setItems($items);
    }

    public function update(EntityIdInterface $entity)
    {
        $items = $this->getItems();
        /*$item = EntityHelper::toArray($entity);
        $key = array_search($item, $items);
        if ($key !== FALSE) {
            $array[$key] = ;
            unset();
        }*/
        $this->setItems($items);
    }

    public function deleteById($id)
    {
        $items = $this->getItems();

        $this->setItems($items);
    }

    public function deleteByCondition(array $condition)
    {
        $items = $this->getItems();

        $this->setItems($items);
    }
}
