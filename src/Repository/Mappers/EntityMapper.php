<?php

namespace ZnCore\Domain\Repository\Mappers;

use ZnCore\Entity\Helpers\EntityHelper;
use ZnCore\Domain\Repository\Interfaces\MapperInterface;

class EntityMapper implements MapperInterface
{

    private $attribute;
    private $entityClass;

    public function __construct(string $attribute, string $entityClass)
    {
        $this->attribute = $attribute;
        $this->entityClass = $entityClass;
    }

    public function encode($entityAttributes)
    {
        $foreignEntity = $entityAttributes[$this->attribute] ?: null;
        if ($foreignEntity) {
            $entityAttributes[$this->attribute] = EntityHelper::toArray($this->entityClass, $foreignEntity);
        }
        return $entityAttributes;
    }

    public function decode($rowAttributes)
    {
        $foreignEntity = $rowAttributes[$this->attribute] ?: null;
        if ($foreignEntity) {
            $rowAttributes[$this->attribute] = EntityHelper::createEntity($this->entityClass, $foreignEntity);
        }
        return $rowAttributes;
    }
}
