<?php

namespace ZnCore\Domain\Relation\Libs\Types;

use ZnCore\Domain\Collection\Libs\Collection;

interface RelationInterface
{

    public function run(Collection $collection);

}
