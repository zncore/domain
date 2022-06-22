<?php

namespace ZnCore\Domain\Query\Enums;

class OperatorEnum
{

    const NULL = 'NULL';
    const NOT_NULL = 'NOT NULL';
    const EQUAL = '=';
    const NOT_EQUAL = '<>';
    //const NOT_EQUAL = '!=';
    const LESS = '<';
    const LESS_OR_EQUAL = '<=';
    const GREATER = '>';
    const GREATER_OR_EQUAL = '>=';
    const LIKE = 'like';
    const ILIKE = 'ilike';

    /*'=', '<', '>', '<=', '>=', '<>', '!=',
    'like', 'not like', 'between', 'ilike', 'not ilike',
    '~', '&', '|', '#', '<<', '>>', '<<=', '>>=',
    '&&', '@>', '<@', '?', '?|', '?&', '||', '-', '-', '#-',
    'is distinct from', 'is not distinct from',*/

}
