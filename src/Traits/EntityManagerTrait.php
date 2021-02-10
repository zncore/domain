<?php

namespace ZnCore\Domain\Traits;

use ZnCore\Domain\Interfaces\Libs\EntityManagerInterface;

trait EntityManagerTrait
{

    private $entityManager;

    protected function hasEntityManager(): bool
    {
        return isset($this->entityNamager);
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->entityNamager;
    }

    protected function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityNamager = $entityManager;
    }
}
