<?php

namespace ZnCore\Domain\Relations\relations;

use Illuminate\Support\Collection;

interface RelationInterface
{

    public function run(Collection $collection);

}
