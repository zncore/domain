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
use ZnCore\Contract\Domain\Interfaces\Entities\EntityIdInterface;
use ZnCore\Domain\Interfaces\Entity\UniqueInterface;
use ZnCore\Domain\Interfaces\Libs\EntityManagerInterface;
use ZnCore\Domain\Interfaces\Libs\OrmInterface;
use ZnCore\Domain\Interfaces\Repository\CrudRepositoryInterface;
use ZnCore\Domain\Interfaces\Repository\RepositoryInterface;

class ArrayEntityManager implements EntityManagerInterface
{

    private $entities = [];

    public function bindEntity(string $entityClass, string $repositoryInterface): void
    {
        $this->entities[$entityClass] = $repositoryInterface;
//        $this->entityToRepository[$entityClass] = $repositoryInterface;
    }

    public function getEntities(): array
    {
        return $this->entities;
    }

    public function getRepositoryByEntityClass(string $entityClass): RepositoryInterface
    {
        // TODO: Implement getRepositoryByEntityClass() method.
    }

    public function loadEntityRelations(object $entity, array $with)
    {
        // TODO: Implement loadEntityRelations() method.
    }

    public function all(string $entityClass, Query $query = null): Collection
    {
        // TODO: Implement all() method.
    }

    public function count(string $entityClass, Query $query = null): int
    {
        // TODO: Implement count() method.
    }

    public function one(string $entityClass, Query $query = null): EntityIdInterface
    {
        // TODO: Implement one() method.
    }

    public function oneById(string $entityClass, $id, Query $query = null): EntityIdInterface
    {
        // TODO: Implement oneById() method.
    }

    public function remove(EntityIdInterface $entity)
    {
        // TODO: Implement remove() method.
    }

    public function persist(EntityIdInterface $entity): void
    {
        // TODO: Implement persist() method.
    }

    public function insert(EntityIdInterface $entity): void
    {
        // TODO: Implement insert() method.
    }

    public function update(EntityIdInterface $entity): void
    {
        // TODO: Implement update() method.
    }

    public function getRepositoryByClass(string $class): RepositoryInterface
    {
        // TODO: Implement getRepositoryByClass() method.
    }

    public function createEntity(string $entityClassName, $attributes = []): object
    {
        // TODO: Implement createEntity() method.
    }

    public function createEntityCollection(string $entityClassName, array $items): Collection
    {
        // TODO: Implement createEntityCollection() method.
    }

    public function oneByUnique(UniqueInterface $entity): EntityIdInterface
    {
        // TODO: Implement oneByUnique() method.
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
