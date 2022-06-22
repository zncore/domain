<?php

namespace ZnCore\Domain\Repository\Mappers;

use ZnCore\Domain\Repository\Interfaces\MapperInterface;

class JsonMapper implements MapperInterface
{

    private $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    public function encode($entityAttributes)
    {
        foreach ($this->attributes as $attribute) {
            $entityAttributes[$attribute] = json_encode($entityAttributes[$attribute], JSON_UNESCAPED_UNICODE);
        }
        return $entityAttributes;
    }

    public function decode($rowAttributes)
    {
        foreach ($this->attributes as $attribute) {
            $value = $rowAttributes[$attribute] ?? null;
            if ($value) {
                $rowAttributes[$attribute] = json_decode($rowAttributes[$attribute], JSON_OBJECT_AS_ARRAY);
            }
        }
        return $rowAttributes;
    }
}
