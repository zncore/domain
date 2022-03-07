<?php

namespace ZnCore\Domain\Strategies\join\handlers;

use Illuminate\Support\Collection;
use ZnCore\Domain\Libs\Query;
use ZnCore\Domain\Dto\WithDto;
use ZnCore\Domain\Entities\relation\RelationEntity;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnCore\Domain\Helpers\Repository\RelationRepositoryHelper;
use ZnCore\Base\Libs\ArrayTools\Helpers\ArrayIterator;
use Symfony\Component\PropertyAccess\PropertyAccess;

class Many extends Base implements HandlerInterface
{

    public function join(Collection $collection, RelationEntity $relationEntity)
    {
        $values = self::getColumn($collection, $relationEntity->field);

        $query = Query::forge();
        $query->where($relationEntity->foreign->field, $values);

        $relCollection = RelationRepositoryHelper::getAll($relationEntity->foreign, $query);
        return $relCollection;
    }

    public function load(object $entity, WithDto $w, $relCollection): RelationEntity
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $fieldValue = $propertyAccessor->getValue($entity, $w->relationConfig->field);
        //$fieldValue = $entity->{$w->relationConfig->field};
        if (empty($fieldValue)) {
            return $w->relationConfig;
        }
        $query = Query::forge();
        $query->where($w->relationConfig->foreign->field, $fieldValue);
        $data = ArrayIterator::allFromArray($query, $relCollection);
        $data = self::prepareValue($data, $w);
        $propertyAccessor->setValue($entity, $w->relationName, $data);
        //EntityHelper::setAttribute($entity, $w->relationName, $data);
        //$entity->{$w->relationName} = $data;
        return $w->relationConfig;
    }

}