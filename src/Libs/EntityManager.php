<?php

namespace ZnCore\Domain\Libs;

use App\Organization\Domain\Entities\LanguageEntity;
use Illuminate\Support\Collection;
use Psr\Container\ContainerInterface;
use ZnCore\Base\Exceptions\AlreadyExistsException;
use ZnCore\Base\Exceptions\InvalidConfigException;
use ZnCore\Base\Exceptions\InvalidMethodParameterException;
use ZnCore\Base\Exceptions\NotFoundException;
use ZnCore\Base\Libs\I18Next\Facades\I18Next;
use ZnCore\Domain\Exceptions\UnprocessibleEntityException;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnCore\Domain\Helpers\ValidationHelper;
use ZnCore\Domain\Interfaces\Entity\EntityIdInterface;
use ZnCore\Domain\Interfaces\Entity\UniqueInterface;
use ZnCore\Domain\Interfaces\Libs\EntityManagerInterface;
use ZnCore\Domain\Interfaces\Libs\OrmInterface;
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
     * @throws InvalidConfigException
     */
    public function getRepositoryByEntityClass(string $entityClass): RepositoryInterface
    {
        if (!isset($this->entityToRepository[$entityClass])) {
            $abstract = $this->findInDefinitions($entityClass);
            if ($abstract) {
                $entityClass = $abstract;
            } else {
                throw new InvalidConfigException("Not found \"{$entityClass}\" in entity manager.");
            }
        }
        $class = $this->entityToRepository[$entityClass];
        return $this->getRepositoryByClass($class);
    }

    private function findInDefinitions(string $entityClass)
    {
        if (empty($this->config['definitions'])) {
            return null;
        }
        foreach ($this->config['definitions'] as $abstract => $concrete) {
            if ($concrete == $entityClass) {
                return $abstract;
            }
        }
        return null;
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
        if ($entity->getId()) {
            $repository->deleteById($entity->getId());
        } else {
            $uniqueEntity = $repository->oneByUnique($entity);
            /*if (empty($uniqueEntity)) {
                throw new NotFoundException('Unique entity not found!');
            }*/
            $repository->deleteById($uniqueEntity->getId());
        }
    }

    public function persist(EntityIdInterface $entity): void
    {
        $entityClass = get_class($entity);
        $repository = $this->getRepositoryByEntityClass($entityClass);

        $isUniqueDefined = $entity instanceof UniqueInterface && $entity->unique();

        if ($isUniqueDefined) {
            try {
                $uniqueEntity = $repository->oneByUnique($entity);
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

    protected function checkUniqueExist(EntityIdInterface $entity) {
        if(!$entity instanceof UniqueInterface) {
            return;
        }
        try {
            $uniqueEntity = $this->oneByUnique($entity);
            foreach ($entity->unique() as $group) {
                $isMach = true;
                $fields = [];
                foreach ($group as $fieldName) {
                    if(EntityHelper::getValue($uniqueEntity, $fieldName) != EntityHelper::getValue($entity, $fieldName)) {
                        $isMach = false;
                        break;
                    } else {
                        $fields[] = $fieldName;
                    }
                }
                if($isMach) {
                    $message = I18Next::t('core', 'domain.message.entity_already_exist');
                    $alreadyExistsException = new AlreadyExistsException($message);
                    $alreadyExistsException->setEntity($uniqueEntity);
                    $alreadyExistsException->setFields($fields);
                    throw $alreadyExistsException;
                }
            }
        } catch (NotFoundException $e) {}
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
        $repository = $this->getRepositoryByEntityClass($entityClass);
        $repository->create($entity);
    }

    public function update(EntityIdInterface $entity): void
    {
        $entityClass = get_class($entity);
        $repository = $this->getRepositoryByEntityClass($entityClass);
        $repository->update($entity);
    }

    public function oneByUnique(UniqueInterface $entity): ?EntityIdInterface
    {
        $entityClass = get_class($entity);
        $repository = $this->getRepositoryByEntityClass($entityClass);
        return $repository->oneByUnique($entity);

        /*$unique = $entity->unique();
        foreach ($unique as $uniqueConfig) {
            $query = new Query();
            foreach ($uniqueConfig as $uniqueName) {
                $query->where(Inflector::underscore($uniqueName), EntityHelper::getValue($entity, $uniqueName));
            }
            $all = $repository->all($query);
            if ($all->count() > 0) {
                return $all->first();
                //EntityHelper::setAttributes($entity, EntityHelper::toArray($all->first()));
                //return;
            }
        }
        return null;*/
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
