<?php

namespace ZnCore\Domain\Libs;

use ZnCore\Domain\Interfaces\Libs\EntityManagerConfiguratorInterface;

class EntityManagerConfigurator implements EntityManagerConfiguratorInterface
{

    private $container;
    private $entityToRepository = [];

    public function __construct(
//        ContainerInterface $container
//        EntityManagerInterface $entityManager
    )
    {
//        $this->container = $container;
//        $this->entityManager = $entityManager;
    }

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
