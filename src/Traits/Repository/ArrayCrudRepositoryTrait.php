<?php

namespace ZnCore\Domain\Traits\Repository;

use ZnCore\Domain\Helpers\EntityHelper;
use ZnCore\Domain\Helpers\FilterHelper;
use ZnCore\Domain\Interfaces\Entity\EntityIdInterface;
use ZnCore\Domain\Interfaces\Libs\EntityManagerInterface;
use ZnCore\Domain\Libs\Query;

trait ArrayCrudRepositoryTrait
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
        $items[] = EntityHelper::toArray($entity);
        $this->setItems($items);
    }

    public function update(EntityIdInterface $entity)
    {
        $items = $this->getItems();
        foreach ($items as &$item) {
            if ($entity->getId() == $item['id']) {
                $item = EntityHelper::toArray($entity);
            }
        }
        $this->setItems($items);
    }

    public function deleteById($id)
    {
        $this->deleteByCondition(['id' => $id]);
        /*$items = $this->getItems();
        foreach ($items as &$item) {
            if($entity->getId() == $item['id']) {
                unset($item);
            }
        }
        $this->setItems($items);*/
    }

    public function deleteByCondition(array $condition)
    {
        $items = $this->getItems();
        foreach ($items as &$item) {
            $isMatch = $this->isMatch($item, $condition);
            if ($isMatch) {
                unset($item);
            }
        }
        $this->setItems($items);
    }

    private function isMatch(array $item, array $condition): bool
    {
        foreach ($condition as $conditionAttribute => $conditionValue) {
            if ($item[$conditionAttribute] != $conditionValue) {
                return false;
            }
        }
        return true;
    }
}
