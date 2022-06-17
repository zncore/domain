<?php

namespace ZnCore\Domain\Interfaces\Libs;

interface EntityManagerConfiguratorInterface
{

    public function bindEntity(string $entityClass, string $repositoryInterface): void;

}