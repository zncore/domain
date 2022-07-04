<?php

namespace ZnCore\Domain\Service\Interfaces;

use ZnCore\Domain\Entity\Exceptions\NotFoundException;
use ZnCore\Domain\Entity\Interfaces\EntityIdInterface;
use ZnCore\Domain\Query\Entities\Query;

interface FindOneInterface
{

    /**
     * Получить одну сущность по ID
     * @param $id int ID сущности
     * @param Query|null $query Объект запроса
     * @return object|EntityIdInterface
     * @throws NotFoundException
     */
    public function findOneById($id, Query $query = null): EntityIdInterface;
}