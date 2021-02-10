<?php

namespace ZnCore\Domain\Libs;

use Illuminate\Support\Collection;
use Psr\Container\ContainerInterface;
use ZnCore\Base\Exceptions\InvalidMethodParameterException;
use ZnCore\Base\Legacy\Yii\Helpers\Inflector;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnCore\Domain\Interfaces\Entity\EntityIdInterface;
use ZnCore\Domain\Interfaces\Entity\UniqueInterface;
use ZnCore\Domain\Interfaces\Libs\EntityManagerInterface;
use ZnCore\Domain\Interfaces\Repository\CrudRepositoryInterface;
use ZnCore\Domain\Interfaces\Repository\RepositoryInterface;

class EntityManager implements EntityManagerInterface
{

    private $container;
    private $config;
    private $entityToRepository;
    private static $instance;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public static function getInstance(ContainerInterface $container = null): self
    {
        if (!isset(self::$instance)) {
            if ($container == null) {
                throw new InvalidMethodParameterException('Need Container for create EntityManager');
            }
            self::$instance = new self($container);
        }
        return self::$instance;
    }

    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    public function bindEntity(string $entityClass, string $repositoryInterface): void
    {
        $this->entityToRepository[$entityClass] = $repositoryInterface;
    }

    /**
     * @param string $entityClass
     * @return RepositoryInterface | CrudRepositoryInterface
     */
    public function getRepositoryByEntityClass(string $entityClass): RepositoryInterface
    {
        $class = $this->entityToRepository[$entityClass];
        return $this->getRepositoryByClass($class);
    }

    public function all(string $entityClass, Query $query = null): Collection
    {
        $repository = $this->getRepositoryByEntityClass($entityClass);
        return $repository->all($query);
    }

    public function loadEntityRelations(object $entity, array $with)
    {
        $entityClass = get_class($entity);
        $collection = new Collection([$entity]);
        $repository = $this->getRepositoryByEntityClass($entityClass);
        $repository->loadRelations($collection, $with);
    }

    public function one(string $entityClass, Query $query = null): EntityIdInterface
    {
        $repository = $this->getRepositoryByEntityClass($entityClass);
        return $repository->one($query);
    }

    public function oneById(string $entityClass, $id, Query $query = null): EntityIdInterface
    {
        $repository = $this->getRepositoryByEntityClass($entityClass);
        return $repository->oneById($id, $query);
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
            if($entity instanceof UniqueInterface) {
                $unique = $entity->unique();
                foreach ($unique as $uniqueConfig) {
                    $query = new Query();
                    foreach ($uniqueConfig as $uniqueName) {
                        $query->where(Inflector::underscore($uniqueName), EntityHelper::getValue($entity, $uniqueName));
                    }
                    $all = $repository->all($query);
                    if($all->count() > 0) {
                        $entity = $all->first();
                        //EntityHelper::setAttributes($entity, EntityHelper::toArray($all->first()));
                        return;
                    }
                }
            }
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

    public function beginTransaction()
    {
        // TODO: Implement beginTransaction() method.
    }

    public function rollbackTransaction()
    {
        // TODO: Implement rollbackTransaction() method.
    }

    public function commitTransaction()
    {
        // TODO: Implement commitTransaction() method.
    }
}
