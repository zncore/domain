<?php

namespace ZnCore\Domain\Repository\Interfaces;

use ZnCore\Contract\Common\Exceptions\InvalidMethodParameterException;
use ZnCore\Entity\Exceptions\NotFoundException;
use ZnCore\Entity\Interfaces\EntityIdInterface;
use ZnCore\Entity\Interfaces\UniqueInterface;

interface FindOneUniqueInterface
{

    /**
     * @param UniqueInterface $entity
     * @return EntityIdInterface | object
     * @throws NotFoundException
     * @throws InvalidMethodParameterException
     */
    public function findOneByUnique(UniqueInterface $entity): EntityIdInterface;

}