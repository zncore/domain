<?php

namespace ZnCore\Domain\Base\Repositories;

use Illuminate\Support\Collection;
use ZnCore\Base\Helpers\EnumHelper;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Domain\Entities\Query\Where;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnCore\Domain\Interfaces\Entity\EntityIdInterface;
use ZnCore\Domain\Interfaces\GetEntityClassInterface;
use ZnCore\Domain\Interfaces\ReadAllInterface;
use ZnCore\Domain\Interfaces\Repository\ReadOneInterface;
use ZnCore\Domain\Interfaces\Repository\RepositoryInterface;
use ZnCore\Domain\Libs\Query;

abstract class BaseEnumCrudRepository implements RepositoryInterface, GetEntityClassInterface, ReadAllInterface, ReadOneInterface
{

    abstract public function enumClass(): string;

    public function all(Query $query = null)
    {
        $all = EnumHelper::all($this->enumClass());
        $labels = EnumHelper::getLabels($this->enumClass());
        $list = [];
        foreach ($all as $name => $id) {
            $list[] = [
                'id' => $id,
                'name' => mb_strtolower($name),
                'title' => $labels[$id],
            ];
        }
        $collection = new Collection($list);
        /** @var Where[] $where */
        $where = $query->getParam(Query::WHERE_NEW);
        if ($where) {
            foreach ($where as $condition) {
                $values = ArrayHelper::toArray($condition->value);
                $resultCollection = new Collection();
                foreach ($values as $value) {
                    $filteredCollection = $collection->where($condition->column, $condition->operator, $value);
                    $resultCollection = $resultCollection->concat($filteredCollection);
                }
                $collection = $resultCollection;
            }
        }
        return EntityHelper::createEntityCollection($this->getEntityClass(), $collection->toArray());
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
