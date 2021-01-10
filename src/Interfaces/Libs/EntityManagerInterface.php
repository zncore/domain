<?php

namespace ZnCore\Domain\Interfaces\Libs;

use Illuminate\Support\Collection;
use ZnCore\Domain\Interfaces\Entity\EntityIdInterface;
use ZnCore\Domain\Interfaces\Repository\RepositoryInterface;
use ZnCore\Domain\Libs\Query;

interface EntityManagerInterface
{

    public function getRepositoryByEntityClass(string $entityClass): RepositoryInterface;

    public function all(string $entityClass, Query $query): Collection;

    public function one(string $entityClass, Query $query): EntityIdInterface;

    public function remove(EntityIdInterface $entity);

    public function persist(EntityIdInterface $entity): void;

    public function getRepositoryByClass(string $class): RepositoryInterface;

    public function createEntity(string $entityClassName, $attributes = []): object;

    public function createEntityCollection(string $entityClassName, array $items): Collection;

}