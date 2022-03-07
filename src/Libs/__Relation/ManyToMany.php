<?php

namespace ZnCore\Domain\Libs\Relation;

use Doctrine\Common\Inflector\Inflector;
use Illuminate\Support\Collection;
use ZnCore\Domain\Libs\Query;
use ZnCore\Domain\Helpers\EntityHelper;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ManyToMany
{

    public $selfModel;
    public $selfField;

    public $viaModel;

    public $foreignField;
    public $foreignModel;
    public $foreignContainerField;

    public function run(Collection $collection)
    {
        $selfPkName = $this->selfModel->primaryKey()[0];

        $selfIds = EntityHelper::getColumn($collection, $selfPkName);

        $query = new Query;
        $query->where(Inflector::tableize($this->selfField), $selfIds);
        $viaCollection = $this->viaModel->all($query);

        $targetIds = EntityHelper::getColumn($viaCollection, $this->foreignField);
        $targetIds = array_unique($targetIds);

        $targetQuery = new Query;
        $targetQuery->where($this->foreignModel->primaryKey()[0], $targetIds);
        $targetAllCollection = $this->foreignModel->all($targetQuery);

        $targetPkName = $this->foreignModel->primaryKey()[0];
        $selfCollectionIndexed = EntityHelper::indexingCollection($targetAllCollection, $targetPkName);

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        foreach ($collection as $selfEntity) {
            $relationCollection = new Collection;
            foreach ($viaCollection as $viaEntity) {
                $viaSelfPkValue = $propertyAccessor->getValue($viaEntity, $this->selfField);
                $selfPkValue = $propertyAccessor->getValue($selfEntity, $selfPkName);
                if ($viaSelfPkValue == $selfPkValue) {
                    $viaTargetPkValue = $propertyAccessor->getValue($viaEntity, $this->foreignField);
                    $relationCollection->add($selfCollectionIndexed[$viaTargetPkValue]);
                }
            }
            $propertyAccessor->setValue($selfEntity, $this->foreignContainerField, $relationCollection);
        }
    }

}