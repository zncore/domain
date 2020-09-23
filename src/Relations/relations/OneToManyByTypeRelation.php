<?php

namespace ZnCore\Domain\Relations\relations;

use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Domain\Libs\Query;
use ZnCore\Domain\Relations\helpers\RelationHelper;
use ZnCore\Domain\Relations\interfaces\CrudRepositoryInterface;
use yii\di\Container;

class OneToManyByTypeRelation extends BaseRelation implements RelationInterface
{

    /** Связующее поле */
    public $relationAttribute;

    //public $foreignPrimaryKey = 'id';
    //public $foreignAttribute = 'id';

    protected function loadRelation(&$collection)
    {
        if(is_array($this->relationAttribute)) {
            $conditions = RelationHelper::generateCondition($collection, $this->relationAttribute);
            $foreignRepositoryInstance = $this->getRepositoryInstance();
            $foreignCollection = $this->loadRelationByCondition($conditions, $foreignRepositoryInstance);
            $foreignCollection = ArrayHelper::index($foreignCollection, function ($entity) {
                return RelationHelper::generateRelationIndexItem($this->relationAttribute, $entity, 'cond');
            });
            foreach ($collection as $entity) {
                $relationIndex = RelationHelper::generateRelationIndexItem($this->relationAttribute, $entity);
                if(!empty($relationIndex)) {
                    $relCollection = [];
                    foreach ($foreignCollection as $foreignEntity) {
                        if($foreignEntity->{$this->foreignAttribute} == $relationIndex) {
                            $relCollection[] = $foreignEntity;
                        }
                    }
                    $entity->{$this->relationEntityAttribute} = $relCollection;
                }
            }
        } else {
            $ids = ArrayHelper::getColumn($collection, $this->relationAttribute);
            $ids = array_unique($ids);
            $foreignCollection = $this->loadRelationByIds($ids);
            foreach ($collection as $entity) {
                $relationIndex = $entity->{$this->relationAttribute};
                if(!empty($relationIndex)) {
                    $relCollection = [];
                    foreach ($foreignCollection as $foreignEntity) {
                        if($foreignEntity->{$this->foreignAttribute} == $relationIndex) {
                            $relCollection[] = $foreignEntity;
                        }
                    }
                    $entity->{$this->relationEntityAttribute} = $relCollection;
                }
            }
        }
    }

    protected function loadRelationByCondition(array $conditions, CrudRepositoryInterface $foreignRepositoryInstance): array {
        $query = $this->getQuery();
        $query->andWhere($conditions);
        $collection = $foreignRepositoryInstance->all($query);
        return $collection;
        //return $this->loadCollection($foreignRepositoryInstance, $ids, $query);
    }
}
