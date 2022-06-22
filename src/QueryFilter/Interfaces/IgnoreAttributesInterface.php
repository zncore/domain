<?php

namespace ZnCore\Domain\QueryFilter\Interfaces;

interface IgnoreAttributesInterface
{

    public function ignoreAttributesFromCondition(): array;
}