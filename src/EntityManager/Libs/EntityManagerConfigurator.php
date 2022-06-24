<?php

namespace ZnCore\Domain\EntityManager\Libs;

use ZnCore\Domain\EntityManager\Interfaces\EntityManagerConfiguratorInterface;

class EntityManagerConfigurator implements EntityManagerConfiguratorInterface
{

    private $entityToRepository = [];

    public function bindEntity(string $entityClass, string $repositoryInterface): void
    {
        $this->entityToRepository[$entityClass] = $repositoryInterface;
    }

    public function getConfig(): array
    {
        return $this->entityToRepository;
    }

    public function entityToRepository(string $entityClass)
    {
        return $this->entityToRepository[$entityClass] ?? null;
    }
}