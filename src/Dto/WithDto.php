<?php

namespace ZnCore\Domain\Dto;

use ZnCore\Domain\Libs\Query;
use ZnCore\Domain\Entities\relation\RelationEntity;

class WithDto
{

    /**
     * @var Query
     */
    public $query;
    public $remain;
    public $remainOfRelation;
    public $relationName;

    /**
     * @var RelationEntity
     */
    public $relationConfig;
    public $passed;
    public $withParams;

}