<?php

namespace ZnCore\Domain\EntityManager\Libs;

use ZnCore\Domain\Collection\Libs\Collection;
use ZnCore\Domain\Collection\Interfaces\Enumerable;
use Psr\Container\ContainerInterface;
use ZnCore\Base\Container\Interfaces\ContainerConfiguratorInterface;
use ZnCore\Base\Validation\Exceptions\UnprocessibleEntityException;
use ZnCore\Contract\Common\Exceptions\InvalidConfigException;
use ZnCore\Contract\Common\Exceptions\InvalidMethodParameterException;
use ZnCore\Domain\Entity\Exceptions\AlreadyExistsException;
use ZnCore\Domain\Entity\Exceptions\NotFoundException;
use ZnCore\Domain\Entity\Helpers\EntityHelper;
use ZnCore\Domain\Entity\Interfaces\EntityIdInterface;
use ZnCore\Domain\Entity\Interfaces\UniqueInterface;
use ZnCore\Domain\EntityManager\Interfaces\EntityManagerConfiguratorInterface;
use ZnCore\Domain\EntityManager\Interfaces\EntityManagerInterface;
use ZnCore\Domain\EntityManager\Interfaces\OrmInterface;
use ZnCore\Domain\Repository\Interfaces\CrudRepositoryInterface;
use ZnCore\Domain\Repository\Interfaces\RepositoryInterface;
use ZnLib\Components\I18Next\Facades\I18Next;

class EntityManager implements EntityManagerInterface
{

    private $container;
    private $entityManagerConfigurator;
    private $containerConfigurator;
    private static $instance;

    public function __construct(
        ContainerInterface $container,
        EntityManagerConfiguratorInterface $entityManagerConfigurator,
        ContainerConfiguratorInterface $containerConfigurator
    )
    {
        $this->container = $container;
        $this->entityManagerConfigurator = $entityManagerConfigurator;
        $this->containerConfigurator = $containerConfigurator;
    }

    public static function getInstance(ContainerInterface $container = null): self
    {
        if (!isset(self::$instance)) {
            if ($container == null) {
                throw new InvalidMethodParameterException('Need Container for create EntityManager');
            }
            self::$instance = $container->get(self::class);
//            self::$instance = new self($container);
        }
        return self::$instance;
    }

    /**
     * @param string $entityClass
     * @return RepositoryInterface | CrudRepositoryInterface
     * @throws InvalidConfigException
     */
    public function getRepository(string $entityClass): RepositoryInterface
    {
        $repositoryDefition = $this->entityManagerConfigurator->entityToRepository($entityClass);

        if (!$repositoryDefition) {
            $abstract = $this->findInDefinitions($entityClass);
            if ($abstract) {
                $entityClass = $abstract;
            } else {
                throw new InvalidConfigException("Not found \"{$entityClass}\" in entity manager.");
            }
        }
        $class = $this->entityManagerConfigurator->entityToRepository($entityClass);
        return $this->getRepositoryByClass($class);
    }

    private function findInDefinitions(string $entityClass)
    {
        $containerConfig = $this->containerConfigurator->getConfig();
        if (empty($containerConfig['definitions'])) {
            return null;
        }
        foreach ($containerConfig['definitions'] as $abstract => $concrete) {
            if ($concrete == $entityClass) {
                return $abstract;
            }
        }
        return null;
    }

    public function loadEntityRelations(object $entityOrCollection, array $with): void
    {
        if ($entityOrCollection instanceof Enumerable) {
            $collection = $entityOrCollection;
        } else {
            $collection = new Collection([$entityOrCollection]);
        }

        $entityClass = get_class($collection->first());
        $repository = $this->getRepository($entityClass);
        $repository->loadRelations($collection, $with);
    }

    public function remove(EntityIdInterface $entity): void
    {
        $entityClass = get_class($entity);
        $repository = $this->getRepository($entityClass);
        if ($entity->getId()) {
            $repository->deleteById($entity->getId());
        } else {
            $uniqueEntity = $repository->findOneByUnique($entity);
            /*if (empty($uniqueEntity)) {
                throw new NotFoundException('Unique entity not found!');
            }*/
            $repository->deleteById($uniqueEntity->getId());
        }
    }

    public function persist(EntityIdInterface $entity): void
    {
        $entityClass = get_class($entity);
        $repository = $this->getRepository($entityClass);
        $this->persistViaRepository($entity, $repository);
    }

    public function persistViaRepository(EntityIdInterface $entity, object $repository): void
    {
        $isUniqueDefined = $entity instanceof UniqueInterface && $entity->unique();

        if ($isUniqueDefined) {
            try {
                $uniqueEntity = $repository->findOneByUnique($entity);
                $entity->setId($uniqueEntity->getId());
            } catch (NotFoundException $e) {
            }
        }
        if ($entity->getId() == null) {
            $repository->create($entity);
        } else {
            $repository->update($entity);
        }
    }

    protected function checkUniqueExist(EntityIdInterface $entity)
    {
        if (!$entity instanceof UniqueInterface) {
            return;
        }
        try {
            $uniqueEntity = $this->findOneByUnique($entity);
            foreach ($entity->unique() as $group) {
                $isMach = true;
                $fields = [];
                foreach ($group as $fieldName) {
                    if (EntityHelper::getValue($entity, $fieldName) === null || EntityHelper::getValue($uniqueEntity, $fieldName) != EntityHelper::getValue($entity, $fieldName)) {
                        $isMach = false;
                        break;
                    } else {
                        $fields[] = $fieldName;
                    }
                }
                if ($isMach) {
                    $message = I18Next::t('core', 'domain.message.entity_already_exist');
                    $alreadyExistsException = new AlreadyExistsException($message);
                    $alreadyExistsException->setEntity($uniqueEntity);
                    $alreadyExistsException->setFields($fields);
                    throw $alreadyExistsException;
                }
            }
        } catch (NotFoundException $e) {
        }
    }

    public function insert(EntityIdInterface $entity): void
    {
        try {
            $this->checkUniqueExist($entity);
        } catch (AlreadyExistsException $alreadyExistsException) {
            $e = new UnprocessibleEntityException();
            foreach ($alreadyExistsException->getFields() as $fieldName) {
                $e->add($fieldName, $alreadyExistsException->getMessage());
            }
            throw $e;
        }

        $entityClass = get_class($entity);
        $repository = $this->getRepository($entityClass);
        $repository->create($entity);
    }

    public function update(EntityIdInterface $entity): void
    {
        $entityClass = get_class($entity);
        $repository = $this->getRepository($entityClass);
        $repository->update($entity);
    }

    public function findOneByUnique(UniqueInterface $entity): EntityIdInterface
    {
        $entityClass = get_class($entity);
        $repository = $this->getRepository($entityClass);
        return $repository->findOneByUnique($entity);
    }

    protected function getRepositoryByClass(string $class): RepositoryInterface
    {
        return $this->container->get($class);
    }

    public function createEntity(string $entityClassName, array $attributes = []): object
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
        foreach ($this->ormList as $orm) {
            $orm->beginTransaction();
        }
    }

    public function rollbackTransaction()
    {
        foreach ($this->ormList as $orm) {
            $orm->rollbackTransaction();
        }
    }

    public function commitTransaction()
    {
        foreach ($this->ormList as $orm) {
            $orm->commitTransaction();
        }
    }

    /** @var array | OrmInterface[] */
    private $ormList = [];

    public function addOrm(OrmInterface $orm)
    {
        $this->ormList[] = $orm;
    }
}
