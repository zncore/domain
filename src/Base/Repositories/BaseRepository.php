<?php

namespace ZnCore\Domain\Base\Repositories;

use ZnCore\Base\Libs\EntityManager\Interfaces\EntityManagerInterface;
use ZnCore\Domain\Interfaces\Repository\RepositoryInterface;
use ZnCore\Base\Libs\EntityManager\Traits\EntityManagerAwareTrait;

abstract class BaseRepository implements RepositoryInterface
{

    use EntityManagerAwareTrait;

    public function __construct(EntityManagerInterface $em)
    {
        $this->setEntityManager($em);
    }
}
