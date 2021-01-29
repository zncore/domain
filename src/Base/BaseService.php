<?php

namespace ZnCore\Domain\Base;

use ZnCore\Domain\Helpers\EntityHelper;
use ZnCore\Domain\Interfaces\Traits\CreateEntityInterface;
use ZnCore\Domain\Interfaces\GetEntityClassInterface;
use ZnCore\Base\Helpers\InstanceHelper;
use ZnCore\Domain\Traits\EntityManagerTrait;

abstract class BaseService implements GetEntityClassInterface, CreateEntityInterface
{

    use EntityManagerTrait;

    protected $repository;

    /**
     * @return GetEntityClassInterface
     */
    protected function getRepository()
    {
        if($this->repository) {
            return $this->repository;
        }
        return $this->getEntityManager()->getRepositoryByEntityClass($this->getEntityClass());
    }

    /*protected function setRepository($repository)
    {
        $this->repository = $repository;
    }*/

    public function getEntityClass(): string
    {
        return $this->getRepository()->getEntityClass();
    }

    public function createEntity(array $attributes = [])
    {
        $entityClass = $this->getEntityClass();
        $entityInstance = EntityHelper::createEntity($entityClass, $attributes);
        return $entityInstance;
    }

}