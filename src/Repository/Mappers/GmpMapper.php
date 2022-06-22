<?php

namespace ZnCore\Domain\Repository\Mappers;

use ZnCore\Domain\Repository\Interfaces\MapperInterface;

class GmpMapper implements MapperInterface
{

    private $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;

    }

    public function encode($entityAttributes)
    {
        foreach ($this->attributes as $attribute) {
            $hex = gmp_strval($entityAttributes[$attribute], 16);
            $binary = hex2bin($hex);
            $entityAttributes[$attribute] = base64_encode($binary);
        }
        return $entityAttributes;
    }

    public function decode($rowAttributes)
    {
        foreach ($this->attributes as $attribute) {
            $value = $rowAttributes[$attribute] ?? null;
            if ($value) {
                $binary = base64_decode($rowAttributes[$attribute]);
                $hex = bin2hex($binary);
                $rowAttributes[$attribute] = gmp_init($hex, 16);
            }
        }
        return $rowAttributes;
    }
}
