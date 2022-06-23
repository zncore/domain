<?php

namespace ZnCore\Domain\Repository\Interfaces;

use ZnCore\Base\Exceptions\InvalidMethodParameterException;
use ZnCore\Domain\Entity\Exceptions\NotFoundException;
use ZnCore\Domain\Entity\Interfaces\EntityIdInterface;
use ZnCore\Domain\Query\Entities\Query;

interface FindOneInterface
{

    /**
     * @param $id
     * @param Query|null $query
     * @return EntityIdInterface | object
     * @throws NotFoundException
     * @throws InvalidMethodParameterException
     */
    public function oneById($id, Query $query = null): EntityIdInterface;

}