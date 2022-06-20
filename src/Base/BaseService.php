<?php

namespace ZnCore\Domain\Base;

use ZnCore\Base\Libs\Event\Traits\EventDispatcherTrait;
use ZnCore\Domain\Interfaces\GetEntityClassInterface;
use ZnCore\Domain\Interfaces\Service\CreateEntityInterface;
use ZnCore\Base\Libs\EntityManager\Traits\EntityManagerAwareTrait;
use ZnCore\Base\Libs\Repository\Traits\RepositoryAwareTrait;

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