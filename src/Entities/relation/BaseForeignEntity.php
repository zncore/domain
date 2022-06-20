<?php

namespace ZnCore\Domain\Entities\relation;

use ZnCore\Base\Helpers\DeprecateHelper;
use ZnCore\Domain\Enums\RelationClassTypeEnum;

DeprecateHelper::hardThrow();

/**
 * Class BaseForeignEntity
 *
 * @package ZnCore\Domain\Entities\relation
 *
 * @property $id
 * @property $domain
 * @property $name
 * @property $model
 * @property $classType
 */
abstract class BaseForeignEntity
{

    public $id;
    public $domain;
    public $name;
    public $model;
    public $classType = RelationClassTypeEnum::REPOSITORY;

   /* public function rules()
    {
        return [
            [['classType'], 'in', 'range' => RelationClassTypeEnum::values()],
        ];
    }*/

    public function setId($id)
    {
        list($this->domain, $this->name) = explode('.', $id);
    }

    public function getId()
    {

    }
}