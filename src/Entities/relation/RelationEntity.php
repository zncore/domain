<?php

namespace ZnCore\Domain\Entities\relation;

use ZnCore\Base\Helpers\DeprecateHelper;
use ZnCore\Domain\Enums\RelationEnum;

DeprecateHelper::hardThrow();

/**
 * Class RelationEntity
 *
 * @package ZnCore\Domain\Entities\relation
 *
 * @property $type
 * @property $field
 * @property ForeignEntity $foreign
 * @property ForeignViaEntity $via
 * @property $callback
 */
class RelationEntity
{

    public $type;
    public $field;
    public $foreign;
    public $via;
    public $callback;

    /*public function fieldType()
    {
        return [
            'foreign' => ForeignEntity::class,
            'via' => ForeignViaEntity::class,
        ];
    }

    public function rules()
    {
        return [
            [['type'], 'required'],
            [['type'], 'in', 'range' => RelationEnum::values()],
        ];
    }*/

}
