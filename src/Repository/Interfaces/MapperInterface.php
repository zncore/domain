<?php

namespace ZnCore\Domain\Repository\Interfaces;

use ZnCore\Contract\Encoder\Interfaces\EncoderInterface;

/**
 * Возможность маппинга сущностей
 */
interface MapperInterface extends EncoderInterface
{

    /**
     * Маппинг атрибутов сущность -> хранилище
     *
     * @param array $entityAttributes Массив атрибутов сущности
     * @return array
     */
    public function encode($entityAttributes);

    /**
     * Маппинг атрибутов хранилище -> сущность
     * 
     * @param array $rowAttributes Массив атрибутов записи из БД
     * @return array
     */
    public function decode($rowAttributes);
}
