<?php

namespace ZnCore\Domain\Entities\relation;

use ZnCore\Base\Helpers\DeprecateHelper;

DeprecateHelper::hardThrow();

/**
 * Class ForeignViaEntity
 *
 * @package ZnCore\Domain\Entities\relation
 *
 * @property $self
 * @property $foreign
 */
class ForeignViaEntity extends BaseForeignEntity
{

    public $self;
    public $foreign;

}