<?php

namespace ZnCore\Domain\Interfaces\Repository;

use ZnCore\Base\Exceptions\InvalidMethodParameterException;
use ZnCore\Base\Exceptions\NotFoundException;
use ZnCore\Contract\Domain\Interfaces\Entities\EntityIdInterface;
use ZnCore\Domain\Interfaces\Entity\UniqueInterface;
use ZnCore\Domain\Libs\Query;

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