<?php

namespace ZnCore\Domain\Base\Repositories;

use ZnCore\Domain\Interfaces\GetEntityClassInterface;
use ZnCore\Domain\Interfaces\Libs\EntityManagerInterface;
use ZnCore\Domain\Interfaces\Repository\RepositoryInterface;
use ZnCore\Domain\Traits\EntityManagerTrait;

abstract class BaseRepository implements RepositoryInterface/*, GetEntityClassInterface*/
{

    use EntityManagerTrait;

    public function __construct(EntityManagerInterface $em)
    {
        $this->setEntityManager($em);
    }

    //abstract public function getEntityClass(): string;
}
