<?php

namespace ZnCore\Domain\Strategies\join\handlers;

use Illuminate\Support\Collection;
use ZnCore\Domain\Dto\WithDto;
use ZnCore\Domain\Entities\relation\RelationEntity;
use ZnCore\Domain\Helpers\EntityHelper;
use Symfony\Component\PropertyAccess\PropertyAccess;

class One extends Many implements HandlerInterface
{

    public function join(Collection $collection, RelationEntity $relationEntity)
    {
        $relCollection = parent::join($collection, $relationEntity);
        $foreignField = $relationEntity->foreign->field;

        $collection = EntityHelper::indexingCollection($relCollection, $foreignField);

        /*$propertyAccessor = PropertyAccess::createPropertyAccessor();

        $collection = [];
        foreach ($relCollection as $item) {
            $pkValue = $propertyAccessor->getValue($item, $foreignField);
            $collection[$pkValue] = $item;
        }*/
        return $collection;
    }

    public function load(object $entity, WithDto $w, $relCollection): RelationEntity
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $fieldValue = $propertyAccessor->getValue($entity, $w->relationConfig->field);
        if (empty($fieldValue)) {
            return $w->relationConfig;
        }
        if (array_key_exists($fieldValue, $relCollection)) {
            $data = $relCollection[$fieldValue];
            $data = self::prepareValue($data, $w);
            $propertyAccessor->setValue($entity, $w->relationName, $data);
        }
        return $w->relationConfig;
    }

}