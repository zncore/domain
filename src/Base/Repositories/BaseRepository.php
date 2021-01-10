<?php

namespace ZnCore\Domain\Base\Repositories;

use ZnCore\Domain\Interfaces\GetEntityClassInterface;
use ZnCore\Domain\Interfaces\Libs\EntityManagerInterface;
use ZnCore\Domain\Interfaces\Repository\RepositoryInterface;

abstract class BaseRepository implements RepositoryInterface, GetEntityClassInterface
{

    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    abstract public function getEntityClass(): string;
}
