<?php

namespace ZnCore\Domain\Repository\Base;

use ZnCore\Domain\EntityManager\Interfaces\EntityManagerInterface;
use ZnCore\Domain\Repository\Interfaces\RepositoryInterface;
use ZnCore\Domain\EntityManager\Traits\EntityManagerAwareTrait;

abstract class BaseRepository implements RepositoryInterface
{

    use EntityManagerAwareTrait;

    public function __construct(EntityManagerInterface $em)
    {
        $this->setEntityManager($em);
    }
}
