<?php

namespace ZnCore\Domain\Service\Interfaces;

use ZnCore\Base\Exceptions\NotFoundException;
use ZnCore\Domain\Entity\Interfaces\EntityIdInterface;
use ZnCore\Domain\Query\Entities\Query;

interface FindOneInterface
{

    /**
     * @param $id
     * @param Query|null $query
     * @return object|EntityIdInterface
     * @throws NotFoundException
     */
    public function oneById($id, Query $query = null): EntityIdInterface;

}