<?php

namespace ZnCore\Domain\Service\Base;

use ZnCore\Base\Libs\EventDispatcher\Traits\EventDispatcherTrait;
use ZnCore\Domain\Domain\Interfaces\GetEntityClassInterface;
use ZnCore\Domain\Service\Interfaces\CreateEntityInterface;
use ZnCore\Domain\EntityManager\Traits\EntityManagerAwareTrait;
use ZnCore\Domain\Repository\Traits\RepositoryAwareTrait;

abstract class BaseService implements GetEntityClassInterface, CreateEntityInterface
{

    use EventDispatcherTrait;
    use EntityManagerAwareTrait;
    use RepositoryAwareTrait;

    public function getEntityClass(): string
    {
        return $this->getRepository()->getEntityClass();
    }

    public function createEntity(array $attributes = [])
    {
        $entityClass = $this->getEntityClass();
        return $this
            ->getEntityManager()
            ->createEntity($entityClass, $attributes);

        /*if (DeprecateHelper::isStrictMode()) {
            return $this
                ->getEntityManager()
                ->createEntity($entityClass, $attributes);
        } else {
            $entityInstance = EntityHelper::createEntity($entityClass, $attributes);
            return $entityInstance;
        }*/
    }
}