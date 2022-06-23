<?php

namespace ZnCore\Domain\Repository\Interfaces;

use ZnCore\Contract\Common\Exceptions\InvalidMethodParameterException;
use ZnCore\Domain\Entity\Exceptions\NotFoundException;
use ZnCore\Domain\Entity\Interfaces\EntityIdInterface;
use ZnCore\Domain\Entity\Interfaces\UniqueInterface;

interface FindOneUniqueInterface
{

    /**
     * @param UniqueInterface $entity
     * @return EntityIdInterface | object
     * @throws NotFoundException
     * @throws InvalidMethodParameterException
     */
    public function oneByUnique(UniqueInterface $entity): EntityIdInterface;

}