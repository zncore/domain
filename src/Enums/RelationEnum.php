<?php

namespace ZnCore\Domain\Enums;

use ZnCore\Domain\Base\BaseEnum;

class RelationEnum extends BaseEnum
{

    const ONE = 'one';
    const MANY = 'many';
    const MANY_TO_MANY = 'many-to-many';
    const CALLBACK = 'callback';

}
