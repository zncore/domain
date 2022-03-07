<?php

namespace ZnCore\Domain\Strategies\join\handlers;

use Illuminate\Support\Collection;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Domain\Libs\Query;
use ZnCore\Domain\Dto\WithDto;
use ZnCore\Domain\Entities\relation\RelationEntity;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnCore\Domain\Helpers\Repository\RelationConfigHelper;
use ZnCore\Domain\Helpers\Repository\RelationRepositoryHelper;
use ZnCore\Base\Libs\ArrayTools\Helpers\ArrayIterator;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ManyToMany extends Base implements HandlerInterface
{

    public function join(Collection $collection, RelationEntity $relationEntity)
    {
        /** @var RelationEntity[] $viaRelations */
        $viaRelations = RelationConfigHelper::getRelationsConfig($relationEntity->via->model);
        $name = $relationEntity->via->self;
        $viaRelationToThis = $viaRelations[$name];
        $values = ArrayHelper::getColumn($collection, $viaRelationToThis->foreign->field);
        $query = Query::forge();
        $query->where($viaRelationToThis->field, $values);
        $relCollection = RelationRepositoryHelper::getAll($relationEntity->via, $query);
        return $relCollection;
    }

    public function load(object $entity, WithDto $w, $relCollection): RelationEntity
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $viaRelations = RelationConfigHelper::getRelationsConfig($w->relationConfig->via->model);
        /** @var RelationEntity $viaRelationToThis */
        $viaRelationToThis = $viaRelations[$w->relationConfig->via->self];
        /** @var RelationEntity $viaRelationToForeign */
        $viaRelationToForeign = $viaRelations[$w->relationConfig->via->foreign];

        $itemValue = $propertyAccessor->getValue($entity, $viaRelationToForeign->foreign->field);

        //$itemValue = EntityHelper::getAttribute($entity, $viaRelationToForeign->foreign->field);
        //$itemValue = $entity->{$viaRelationToForeign->foreign->field};
        $viaQuery = Query::forge();
        $viaQuery->where($viaRelationToThis->field, $itemValue);
        $viaData = ArrayIterator::allFromArray($viaQuery, $relCollection);
        $foreignIds = ArrayHelper::getColumn($viaData, $viaRelationToForeign->field);
        $query = Query::forge();
        $query->where($viaRelationToForeign->foreign->field, $foreignIds);
        $data = RelationRepositoryHelper::getAll($viaRelationToForeign->foreign, $query);
        $data = self::prepareValue($data, $w);
        $propertyAccessor->setValue($entity, $w->relationName, $data);
        //EntityHelper::setAttribute($entity, $w->relationName, $data);
        //$entity->{$w->relationName} = $data;
        return $viaRelationToForeign;
    }
}