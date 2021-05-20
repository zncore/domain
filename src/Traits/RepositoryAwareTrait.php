<?php

namespace ZnCore\Domain\Traits;

use ZnCore\Domain\Interfaces\GetEntityClassInterface;

trait RepositoryAwareTrait
{

    //todo: private
    protected $repository;

    /**
     * @return GetEntityClassInterface
     */
    protected function getRepository(): object
    {
        if ($this->repository) {
            return $this->repository;
        }
        return $this->getEntityManager()->getRepositoryByEntityClass($this->getEntityClass());
    }

    protected function setRepository(object $repository)
    {
        $this->repository = $repository;
    }
}
