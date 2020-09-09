<?php

namespace ZnCore\Domain\Interfaces;

use Illuminate\Support\Collection;
use ZnCore\Domain\Libs\Query;

interface ReadAllInterface
{

    /**
     * @param Query|null $query
     * @return Collection | array
     */
    public function all(Query $query = null);

    public function count(Query $query = null): int;

}