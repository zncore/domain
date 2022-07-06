<?php

namespace ZnCore\Domain\Relation\Libs\Types;

use ZnCore\Collection\Interfaces\Enumerable;

interface RelationInterface
{

    public function run(Enumerable $collection);

}
