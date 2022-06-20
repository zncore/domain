<?php

namespace ZnCore\Domain\Base\Repositories;

use ZnCore\Domain\Interfaces\Libs\EntityManagerInterface;
use ZnCore\Domain\Interfaces\Repository\RepositoryInterface;
use ZnCore\Domain\Traits\EntityManagerTrait;

abstract class BaseRepository implements RepositoryInterface
{

    use EntityManagerTrait;

    public function __construct(EntityManagerInterface $em)
    {
        $this->setEntityManager($em);
    }
}
