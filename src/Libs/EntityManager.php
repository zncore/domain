<?php

namespace ZnCore\Domain\Libs;

use Illuminate\Support\Collection;
use Psr\Container\ContainerInterface;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnCore\Domain\Interfaces\Entity\EntityIdInterface;
use ZnCore\Domain\Interfaces\Libs\EntityManagerInterface;
use ZnCore\Domain\Interfaces\Repository\RepositoryInterface;

class EntityManager implements EntityManagerInterface
{

    private $container;
    private $config;
    private $entityToRepository;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    public function bindEntity(string $entityClass, string $repositoryInterface): void
    {
        $this->entityToRepository[$entityClass] = $repositoryInterface;
    }

    public function getRepositoryByEntityClass(string $entityClass): RepositoryInterface
    {
        $class = $this->entityToRepository[$entityClass];
        return $this->getRepositoryByClass($class);
    }

    public function all(string $entityClass, Query $query): Collection
    {
        $repository = $this->getRepositoryByEntityClass($entityClass);
        return $repository->all($query);
    }

    public function one(string $entityClass, Query $query): EntityIdInterface
    {
        $repository = $this->getRepositoryByEntityClass($entityClass);
        return $repository->one($query);
    }

    public function remove(EntityIdInterface $entity)
    {
        $entityClass = get_class($entity);
        $repository = $this->getRepositoryByEntityClass($entityClass);
        $repository->deleteById($entity->getId());
    }

    public function persist(EntityIdInterface $entity): void
    {
        $entityClass = get_class($entity);
        $repository = $this->getRepositoryByEntityClass($entityClass);
        if ($entity->getId() === null) {
            $repository->create($entity);
        } else {
            $repository->update($entity);
        }
    }

    public function getRepositoryByClass(string $class): RepositoryInterface
    {
        return $this->container->get($class);
    }

    public function createEntity(string $entityClassName, $attributes = []): object
    {
        $entityInstance = $this->container->get($entityClassName);
        if ($attributes) {
            EntityHelper::setAttributes($entityInstance, $attributes);
        }
        return $entityInstance;
    }

    public function createEntityCollection(string $entityClassName, array $items): Collection
    {
        $collection = new Collection();
        foreach ($items as $item) {
            $entityInstance = $this->createEntity($entityClassName, $item);
            $collection->add($entityInstance);
        }
        return $collection;
    }
}
