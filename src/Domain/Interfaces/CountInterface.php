<?php

namespace ZnCore\Domain\Domain\Interfaces;

use ZnCore\Query\Entities\Query;

interface CountInterface extends \Countable
{

    /**
     * Посчитать количество записей в хранилище
     * @param Query|null $query Объект запроса
     * @return int
     */
    public function count(Query $query = null): int;

}
