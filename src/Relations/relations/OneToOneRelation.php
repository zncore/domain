<?php

namespace ZnCore\Domain\Relations\relations;

use Symfony\Component\PropertyAccess\PropertyAccess;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnCore\Domain\Libs\Query;
use ZnCore\Domain\Relations\interfaces\CrudRepositoryInterface;
use yii\di\Container;

class OneToOneRelation extends BaseRelation implements RelationInterface
{

    /** Связующее поле */
    public $relationAttribute;

    //public $foreignPrimaryKey = 'id';
    //public $foreignAttribute = 'id';

    protected function loadRelation(&$collection)
    {
        $ids = EntityHelper::getColumn($collection, $this->relationAttribute);
        $ids = array_unique($ids);
        $foreignCollection = $this->loadRelationByIds($ids);
        $foreignCollection = EntityHelper::indexingCollection($foreignCollection, $this->foreignAttribute);
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        foreach ($collection as $entity) {
            $relationIndex = $propertyAccessor->getValue($entity, $this->relationAttribute);
            if(!empty($relationIndex)) {
                $value = $foreignCollection[$relationIndex];
                if($this->matchCondition($value)) {
                    $propertyAccessor->setValue($entity, $this->relationEntityAttribute, $value);
                }
            }
        }
    }

    protected function matchCondition($row) {
        if(empty($this->condition)) {
            return true;
        }
        foreach ($this->condition as $key => $value) {
            if(empty($row[$key])) {
                return false;
            }
            if($row[$key] !== $this->condition[$key]) {
                return false;
            }
        }
        return true;
    }
}
