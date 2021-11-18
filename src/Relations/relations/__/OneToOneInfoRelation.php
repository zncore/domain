<?php

namespace ZnCore\Domain\Relations\relations;

use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;

class OneToOneInfoRelation extends OneToOneRelation
{

    protected function loadRelation(&$collection)
    {
        $infoAttributes = ArrayHelper::getColumn($collection, 'info');
        $ids = [];
        foreach ($infoAttributes as $attribute) {
            if (isset($attribute[$this->relationAttribute])) {
                $ids[] = $attribute[$this->relationAttribute];
            }
        }
        $ids = array_unique($ids);
        $foreignCollection = $this->loadRelationByIds($ids);
        $foreignCollectionByIndex = [];
        foreach ($foreignCollection as $item) {
            $foreignCollectionByIndex[$item->{$this->foreignAttribute}] = $item;
        }
        foreach ($collection as $entity) {
            $relationIndex = $entity->infoJson[$this->relationAttribute];
            if(!empty($relationIndex)) {
                $entity->{$this->relationEntityAttribute} = ArrayHelper::getValue($foreignCollectionByIndex, $relationIndex);
            }
        }
    }
}