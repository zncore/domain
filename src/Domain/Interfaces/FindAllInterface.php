<?php

namespace ZnCore\Domain\Domain\Interfaces;

use ZnCore\Domain\Collection\Interfaces\Enumerable;
use ZnCore\Domain\Query\Entities\Query;

interface FindAllInterface
{

    /**
     * Получить коллекцию сущностей из хранилища
     * @param Query|null $query Объект запроса
     * @return Enumerable|array
     */
    public function findAll(Query $query = null): Enumerable;

}
