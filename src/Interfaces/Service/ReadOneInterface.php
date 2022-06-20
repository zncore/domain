<?php

namespace ZnCore\Domain\Interfaces\Service;

use ZnCore\Contract\Domain\Interfaces\Entities\EntityIdInterface;
use ZnCore\Base\Libs\Query\Entities\Query;
use ZnCore\Base\Exceptions\NotFoundException;

interface ReadOneInterface
{

    /**
     * @param $id
     * @param Query|null $query
     * @return object|EntityIdInterface
     * @throws NotFoundException
     */
    public function oneById($id, Query $query = null);

}