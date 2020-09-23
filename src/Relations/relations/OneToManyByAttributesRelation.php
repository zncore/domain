<?php

namespace ZnCore\Domain\Relations\relations;

use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Domain\Relations\interfaces\CrudRepositoryInterface;

class OneToManyByAttributesRelation extends OneToManyRelation
{
    public $relationAttributes;

    public function loadRelation(&$collection)
    {
        if (!$this->relationAttributes) {
            $ids = ArrayHelper::getColumn($collection, $this->relationAttribute);
            $ids = array_unique($ids);
            $foreignCollection = $this->loadRelationByIds($ids);
        } else {
            $condition = ['AND'];
            foreach ($this->relationAttributes as $entityAttribute => $foreignAttribute) {
                if ($entityAttribute == $this->relationAttribute) {
                    $ids = ArrayHelper::getColumn($collection, $entityAttribute);
                    $ids = array_values($ids);
                } else {
                    $entityAttributes = ArrayHelper::getColumn($collection, $entityAttribute);
                    $entityAttributes = array_values($entityAttributes);
                    $condition[] = ['in', $foreignAttribute, $entityAttributes];
                }
            }
            $foreignCollection = $this->loadRelationByIds($ids, $condition);
        }
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

    protected function loadRelationByIds(array $ids, array $condition = [])
    {
        $foreignRepositoryInstance = $this->getRepositoryInstance();
        //$primaryKey = $foreignRepositoryInstance->primaryKey()[0];
        $query = $this->getQuery();
        $query->andWhere(['in', $this->foreignAttribute, array_values($ids)]);
        if (sizeof($condition)) {
            $query->andWhere($condition);
        }
        return $this->loadCollection($foreignRepositoryInstance, $ids, $query);
    }

    protected function loadCollection(CrudRepositoryInterface $foreignRepositoryInstance, array $ids, \yii\db\Query $query): array
    {
        return $foreignRepositoryInstance->all($query);
    }
}