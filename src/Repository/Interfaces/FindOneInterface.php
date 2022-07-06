<?php

namespace ZnCore\Domain\Repository\Interfaces;

use ZnCore\Contract\Common\Exceptions\InvalidMethodParameterException;
use ZnCore\Entity\Exceptions\NotFoundException;
use ZnCore\Entity\Interfaces\EntityIdInterface;
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
//    public function findOneById($id, Query $query = null): EntityIdInterface;

    /**
     * @param $id
     * @param Query|null $query
     * @return EntityIdInterface | object
     * @throws NotFoundException
     * @throws InvalidMethodParameterException
     */
    public function findOneById($id, Query $query = null): EntityIdInterface;

}