<?php

namespace ZnCore\Domain\Interfaces\Repository;

use ZnCore\Base\Exceptions\InvalidMethodParameterException;
use ZnCore\Base\Exceptions\NotFoundException;
use ZnCore\Contract\Domain\Interfaces\Entities\EntityIdInterface;
use ZnCore\Base\Libs\Entity\Interfaces\UniqueInterface;
use ZnCore\Base\Libs\Query\Entities\Query;

interface ReadOneUniqueInterface
{

    /**
     * @param UniqueInterface $entity
     * @return EntityIdInterface | object
     *
     * @throws NotFoundException
     * @throws InvalidMethodParameterException
     */
    public function oneByUnique(UniqueInterface $entity): EntityIdInterface;

}