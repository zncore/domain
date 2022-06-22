<?php

namespace ZnCore\Domain\Relation\Libs\Types;

use Illuminate\Support\Collection;

interface RelationInterface
{

    public function run(Collection $collection);

}
