<?php

namespace ZnCore\Domain\Interfaces\Repository;

use ZnCore\Base\Exceptions\InvalidMethodParameterException;
use ZnCore\Contract\Domain\Interfaces\Entities\EntityIdInterface;
use ZnCore\Base\Libs\Query\Entities\Query;
use ZnCore\Base\Exceptions\NotFoundException;

interface ReadOneInterface
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