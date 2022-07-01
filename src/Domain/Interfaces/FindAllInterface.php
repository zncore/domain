<?php

namespace ZnCore\Domain\Domain\Interfaces;

use Illuminate\Support\Enumerable;
use ZnCore\Domain\Query\Entities\Query;

interface FindAllInterface
{

    public function findAll(Query $query = null): Enumerable;

}
