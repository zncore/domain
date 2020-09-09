<?php

namespace ZnCore\Domain\Strategies\join\handlers;

use Illuminate\Support\Collection;
use ZnCore\Domain\Dto\WithDto;
use ZnCore\Domain\Entities\relation\RelationEntity;

interface HandlerInterface
{

    public function join(Collection $collection, RelationEntity $relationEntity);

    public function load(object $entity, WithDto $w, $relCollection): RelationEntity;

}
