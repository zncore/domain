<?php

namespace ZnCore\Domain\QueryFilter\Interfaces;

use ZnCore\Domain\Query\Entities\Query;

/**
 * Формирование параметров запроса из фильтра
 */
interface ForgeQueryByFilterInterface
{

    /**
     * Формирование параметров запроса из фильтра
     * @param object $filterModel
     * @param Query $query
     * @return mixed
     */
    public function forgeQueryByFilter(object $filterModel, Query $query);
}