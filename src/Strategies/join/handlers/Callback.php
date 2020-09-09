<?php

namespace ZnCore\Domain\Strategies\join\handlers;

use Illuminate\Support\Collection;
use ZnCore\Domain\Dto\WithDto;
use ZnCore\Domain\Entities\relation\RelationEntity;

class Callback extends Base implements HandlerInterface
{

    public function join(Collection $collection, RelationEntity $relationEntity)
    {
        call_user_func_array($relationEntity->callback, [$collection]);

    }

    public function load(object $entity, WithDto $w, $relCollection): RelationEntity
    {

    }

}