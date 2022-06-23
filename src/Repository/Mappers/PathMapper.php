<?php

namespace ZnCore\Domain\Repository\Mappers;

use ZnCore\Base\Arr\Helpers\ArrayHelper;
use ZnCore\Domain\Repository\Interfaces\MapperInterface;

class PathMapper implements MapperInterface
{

    private $map;
    private $isRemoveOldValue;

    public function __construct(array $map, bool $isRemoveOldValue = true)
    {
        $this->map = $map;
        $this->isRemoveOldValue = $isRemoveOldValue;
    }

    public function encode($entityAttributes)
    {
        return $entityAttributes;
    }

    public function decode($rowAttributes)
    {
        foreach ($this->map as $toPath => $fromPath) {
            $value = ArrayHelper::getValue($rowAttributes, $fromPath);
            ArrayHelper::setValue($rowAttributes, $toPath, $value);
            if ($this->isRemoveOldValue) {
                ArrayHelper::removeItem($rowAttributes, $fromPath);
            }
        }
        return $rowAttributes;
    }
}
