<?php

namespace ZnCore\Domain\Libs;

use Psr\Container\ContainerInterface;
use ZnCore\Domain\Interfaces\Libs\EntityManagerConfiguratorInterface;
use ZnCore\Domain\Interfaces\Libs\EntityManagerInterface;

class EntityManagerConfigurator implements EntityManagerConfiguratorInterface
{

    private $container;
    private $entityManager;
    private $config;
    private $entityToRepository;
    private static $instance;

    public function __construct(
        ContainerInterface $container,
        EntityManagerInterface $entityManager
    )
    {
        $this->container = $container;
        $this->entityManager = $entityManager;
    }

    public function bindEntity(string $entityClass, string $repositoryInterface): void
    {
        $this->entityToRepository[$entityClass] = $repositoryInterface;
        $this->entityManager->bindEntity($entityClass, $repositoryInterface);
    }
}
