<?php

namespace ZnCore\Domain\EntityManager\Interfaces;

interface EntityManagerConfiguratorInterface
{

    public function bindEntity(string $entityClass, string $repositoryInterface): void;

    public function getConfig(): array;
}
