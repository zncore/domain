<?php

namespace ZnCore\Domain\Entities;

use Exception;
use ZnCore\Domain\Interfaces\Entity\EntityAttributesInterface;
use ZnCore\Contract\Domain\Interfaces\Entities\EntityIdInterface;
use ZnCore\Domain\Interfaces\Entity\ValidateEntityInterface;
use ZnLib\Telegram\Domain\Facades\Bot;

class DynamicEntity implements ValidateEntityInterface, EntityIdInterface, EntityAttributesInterface
{
    
    protected $_attributes = [];
    protected $_validationRules = [];

    public function __construct(object $entityEntity = null, array $attributes = [])
    {
        if ($entityEntity) {
            $this->_attributes = $entityEntity->getAttributeNames();
        } else {
            $this->_attributes = $attributes;
        }
        if ($entityEntity) {
            $this->_validationRules = $entityEntity->getRules();
        }
    }

    public function attributes(): array
    {
        return $this->_attributes;
    }

    public function validationRules(): array
    {
        return $this->_validationRules;
    }

    public function __get(string $attribute)
    {
        $this->checkHasAttribute($attribute);
        return $this->{$attribute} ?? null;
    }

    public function __set(string $attribute, $value)
    {
//        $this->checkHasAttribute($attribute);
        $this->{$attribute} = $value;
    }

    public function __call(string $name, array $arguments)
    {
        $method = substr($name, 0, 3);
        $attributeName = substr($name, 3);
        $attributeName = lcfirst($attributeName);
        if ($method == 'get') {
            return $this->__get($attributeName);
        } elseif ($method == 'set') {
            $this->__set($attributeName, $arguments[0]);
            return $this;
        }
        return null;
    }

    private function checkHasAttribute(string $attribute)
    {
        $has = in_array($attribute, $this->_attributes);
        if (!$has) {
            throw new Exception('Not found attribute "' . $attribute . '"!');
        }
    }

    public function setId($value): void
    {
        $this->id = $value;
    }

    public function getId()
    {
        return $this->id;
    }
}
