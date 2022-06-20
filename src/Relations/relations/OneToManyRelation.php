<?php

namespace ZnCore\Domain\Relations\relations;

use Illuminate\Support\Collection;
use Symfony\Component\PropertyAccess\PropertyAccess;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Domain\Interfaces\ReadAllInterface;
use ZnCore\Base\Libs\Query\Entities\Query;
use ZnCore\Base\Libs\Entity\Helpers\EntityHelper;
use ZnCore\Domain\Relations\interfaces\CrudRepositoryInterface;
use yii\di\Container;

class OneToManyRelation extends BaseRelation implements RelationInterface
{

    /** Связующее поле */
    public $relationAttribute;

    //public $foreignPrimaryKey = 'id';
    //public $foreignAttribute = 'id';

    protected function loadRelation(Collection $collection)
    {
        $ids = EntityHelper::getColumn($collection, $this->relationAttribute);
        $ids = array_unique($ids);
        $foreignCollection = $this->loadRelationByIds($ids);
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        foreach ($collection as $entity) {
            $relationIndex = $propertyAccessor->getValue($entity, $this->relationAttribute);
            if(!empty($relationIndex)) {
                $relCollection = [];
                foreach ($foreignCollection as $foreignEntity) {
                    $foreignValue = $propertyAccessor->getValue($foreignEntity, $this->foreignAttribute);
                    if($foreignValue == $relationIndex) {
                        $relCollection[] = $foreignEntity;
                    }
                }
                $value = $relCollection;
                $value = $this->getValueFromPath($value);
                $propertyAccessor->setValue($entity, $this->relationEntityAttribute, new Collection($value));
            }
        }
    }

    protected function loadCollection(ReadAllInterface $foreignRepositoryInstance, array $ids, Query $query): Collection {
        //$query->limit(count($ids));
        $collection = $foreignRepositoryInstance->all($query);
        return $collection;
    }
}
