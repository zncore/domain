<?php

namespace ZnCore\Domain\Base;

use ZnCore\Base\Libs\Event\Traits\EventDispatcherTrait;
use ZnCore\Domain\Interfaces\GetEntityClassInterface;
use ZnCore\Domain\Interfaces\Service\CreateEntityInterface;
use ZnCore\Domain\Traits\EntityManagerTrait;
use ZnCore\Domain\Traits\RepositoryAwareTrait;

abstract class BaseService implements GetEntityClassInterface, CreateEntityInterface
{

    use EventDispatcherTrait;
    use EntityManagerTrait;
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