<?php

namespace ZnCore\Domain\Repository\Mappers;

use ZnCore\Domain\Repository\Interfaces\MapperInterface;

class BinaryMapper implements MapperInterface
{

    private $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    public function encode($entityAttributes)
    {
        foreach ($this->attributes as $attribute) {
            $entityAttributes[$attribute] = base64_encode($entityAttributes[$attribute]);
        }
        return $entityAttributes;
    }

    public function decode($rowAttributes)
    {
        foreach ($this->attributes as $attribute) {
            $value = $rowAttributes[$attribute] ?? null;
            if ($value) {
                $rowAttributes[$attribute] = base64_decode($rowAttributes[$attribute]);
            }
        }
        return $rowAttributes;
    }
}
