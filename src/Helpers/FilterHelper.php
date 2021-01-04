<?php

namespace ZnCore\Domain\Helpers;

use Illuminate\Support\Collection;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Domain\Entities\Query\Where;
use ZnCore\Domain\Libs\Query;

class FilterHelper
{

    public static function filterItems(array $items, Query $query): array
    {
        $collection = new Collection($items);
        /** @var Where[] $whereArray */
        $whereArray = $query->getParam(Query::WHERE_NEW);
        if ($whereArray) {
            $collection = self::filterItemsByCondition($collection, $whereArray);
        }
        return $collection->toArray();
    }

    private static function filterItemsByCondition(Collection $collection, array $whereArray): Collection
    {
        foreach ($whereArray as $where) {
            $values = ArrayHelper::toArray($where->value);
            $resultCollection = new Collection();
            foreach ($values as $value) {
                $filteredCollection = $collection->where($where->column, $where->operator, $value);
                $resultCollection = $resultCollection->concat($filteredCollection);
            }
            $collection = $resultCollection;
        }
        return $collection;
    }
}
