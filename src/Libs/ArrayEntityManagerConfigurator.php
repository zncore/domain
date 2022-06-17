<?php

namespace ZnCore\Domain\Libs;

use ZnCore\Domain\Interfaces\Libs\EntityManagerConfiguratorInterface;

class ArrayEntityManagerConfigurator implements EntityManagerConfiguratorInterface
{

    private $entities = [];

    public function bindEntity(string $entityClass, string $repositoryInterface): void
    {
        $this->entities[$entityClass] = $repositoryInterface;
//        $this->entityToRepository[$entityClass] = $repositoryInterface;
    }

    public function getConfig(): array {
        return $this->entities;
    }

    /*public function getEntities(): array
    {
        return $this->entities;
    }*/
}
