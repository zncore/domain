<?php

namespace ZnCore\Domain\Dto;

use ZnCore\Base\Helpers\DeprecateHelper;
use ZnCore\Base\Libs\Query\Entities\Query;
use ZnCore\Domain\Entities\relation\RelationEntity;

DeprecateHelper::hardThrow();

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