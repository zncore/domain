<?php

namespace ZnCore\Domain\Libs\Relation;

use Illuminate\Support\Collection;
use ZnCore\Domain\Libs\Query;
use ZnCore\Domain\Helpers\EntityHelper;
use Symfony\Component\PropertyAccess\PropertyAccess;

class OneToOne
{

    public $foreignContainerField;
    //public $selfModel;

    public $foreignField;
    public $foreignModel;

    public function run(Collection $collection)
    {
        //$selfPkName = $this->selfModel->primaryKey()[0];
        $targetIds = EntityHelper::getColumn($collection, $this->foreignField);
        $targetIds = array_unique($targetIds);
        $targetPkName = $this->foreignModel->primaryKey()[0];
        $query = new Query;
        $query->where($targetPkName, $targetIds);
        $targetCollection = $this->foreignModel->all($query);
        $targetCollection = EntityHelper::indexingCollection($targetCollection, $targetPkName);
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        foreach ($collection as $selfEntity) {
            $targetPkValue = $propertyAccessor->getValue($selfEntity, $this->foreignField);
            $propertyAccessor->setValue($selfEntity, $this->foreignContainerField, $targetCollection[$targetPkValue]);

        }
    }

}