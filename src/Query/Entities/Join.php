<?php

namespace ZnCore\Domain\Query\Entities;

use ZnCore\Domain\Query\Enums\OperatorEnum;

class Join
{

    public $table;
    public $first;
    public $operator;
    public $second;
    public $type;
    public $where;

    public function __construct($table, $first, $second = null, $type = 'inner', $where = false, $operator = OperatorEnum::EQUAL)
    {
        $this->table = $table;
        $this->first = $first;
        $this->operator = $operator;
        $this->second = $second;
        $this->type = $type;
        $this->where = $where;
    }
}
