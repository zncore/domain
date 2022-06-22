<?php

namespace ZnCore\Domain\Domain\Interfaces;

use Illuminate\Support\Enumerable;
use ZnCore\Domain\Query\Entities\Query;

interface FindAllInterface
{

    /**
     * @param Query|null $query
     * @return Enumerable|array
     */
    public function all(Query $query = null): Enumerable;

}
