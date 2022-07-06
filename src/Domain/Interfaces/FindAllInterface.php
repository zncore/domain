<?php

namespace ZnCore\Domain\Domain\Interfaces;

use ZnCore\Collection\Interfaces\Enumerable;
use ZnCore\Query\Entities\Query;

interface FindAllInterface
{

    /**
     * Получить коллекцию сущностей из хранилища
     * @param Query|null $query Объект запроса
     * @return Enumerable|array
     */
    public function findAll(Query $query = null): Enumerable;

}
