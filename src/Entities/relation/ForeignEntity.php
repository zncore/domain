<?php

namespace ZnCore\Domain\Entities\relation;

/**
 * Class ForeignEntity
 *
 * @package ZnCore\Domain\Entities\relation
 *
 * @property $field
 * @property $value
 */
class ForeignEntity extends BaseForeignEntity
{

    public $field = 'id';
    public $value;

}