<?php

namespace ZnCore\Domain\Base;

use ZnCore\Domain\Helpers\EntityHelper;
use ZnCore\Domain\Interfaces\Traits\CreateEntityInterface;
use ZnCore\Domain\Interfaces\GetEntityClassInterface;
use ZnCore\Base\Helpers\InstanceHelper;

abstract class BaseService implements GetEntityClassInterface, CreateEntityInterface
{

    protected $repository;

    /**
     * @return GetEntityClassInterface
     */
    protected function getRepository()
    {
        return $this->repository;
    }

    protected function setRepository($repository)
    {
        $this->repository = $repository;
    }

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