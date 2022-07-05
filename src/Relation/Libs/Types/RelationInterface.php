<?php

namespace ZnCore\Domain\Relation\Libs\Types;

use ZnCore\Domain\Collection\Interfaces\Enumerable;

interface RelationInterface
{

    public function run(Enumerable $collection);

}
