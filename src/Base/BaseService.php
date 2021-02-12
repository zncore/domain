<?php

namespace ZnCore\Domain\Base;

use ZnCore\Base\Helpers\DeprecateHelper;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnCore\Domain\Interfaces\Traits\CreateEntityInterface;
use ZnCore\Domain\Interfaces\GetEntityClassInterface;
use ZnCore\Base\Helpers\InstanceHelper;
use ZnCore\Domain\Traits\EntityManagerTrait;
use ZnCore\Domain\Traits\RepositoryAwareTrait;

abstract class BaseService implements GetEntityClassInterface, CreateEntityInterface
{

    use EntityManagerTrait;
    use RepositoryAwareTrait;

    public function getEntityClass(): string
    {
        return $this->getRepository()->getEntityClass();
    }

    public function createEntity(array $attributes = [])
    {
        $entityClass = $this->getEntityClass();
        if(DeprecateHelper::isStrictMode()) {
            return $this->getEntityManager()->createEntity($this->getEntityClass());
        } else {
            $entityInstance = EntityHelper::createEntity($entityClass, $attributes);
            return $entityInstance;
        }
    }
}