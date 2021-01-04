<?php

namespace ZnCore\Domain\Base\Repositories;

use ZnCore\Domain\Interfaces\GetEntityClassInterface;
use ZnCore\Domain\Interfaces\Repository\RepositoryInterface;
use ZnCore\Domain\Libs\EntityManager;

abstract class BaseRepository implements RepositoryInterface, GetEntityClassInterface
{

    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    abstract public function getEntityClass(): string;
}
