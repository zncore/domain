<?php

namespace ZnCore\Domain\Repository\Base;

use ZnCore\EntityManager\Interfaces\EntityManagerInterface;
use ZnCore\EntityManager\Traits\EntityManagerAwareTrait;
use ZnCore\Domain\Repository\Interfaces\RepositoryInterface;

abstract class BaseRepository implements RepositoryInterface
{

    use EntityManagerAwareTrait;

    public function __construct(EntityManagerInterface $em)
    {
        $this->setEntityManager($em);
    }
}
