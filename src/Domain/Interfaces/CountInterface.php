<?php

namespace ZnCore\Domain\Domain\Interfaces;

use ZnCore\Domain\Query\Entities\Query;

interface CountInterface extends \Countable
{

    public function count(Query $query = null): int;

}
