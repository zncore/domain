<?php

namespace ZnCore\Domain\Entities\Query;

use ZnCore\Domain\Enums\OperatorEnum;

class Where
{

    public $column;
    public $operator;
    public $value;
    public $boolean;
    public $not;

    public function __construct($column = null,  $value = null, $operator = OperatorEnum::EQUAL, $boolean = 'and', $not = false)
    {
        $this->column = $column;
        $this->value = $value;
        $this->operator = $operator;
        $this->boolean = $boolean;
        $this->not = $not;
    }
}
