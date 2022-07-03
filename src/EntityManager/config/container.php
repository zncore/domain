<?php

use Psr\Container\ContainerInterface;
use ZnCore\Base\Container\Interfaces\ContainerConfiguratorInterface;
use ZnCore\Domain\EntityManager\Interfaces\EntityManagerConfiguratorInterface;
use ZnCore\Domain\EntityManager\Interfaces\EntityManagerInterface;
use ZnCore\Domain\EntityManager\Libs\EntityManager;
use ZnCore\Domain\EntityManager\Libs\EntityManagerConfigurator;

return function (ContainerConfiguratorInterface $containerConfigurator) {
    $containerConfigurator->singleton(EntityManagerInterface::class, function (ContainerInterface $container) {
        $em = EntityManager::getInstance($container);
//            $eloquentOrm = $container->get(EloquentOrm::class);
//            $em->addOrm($eloquentOrm);
        return $em;
    });

    $containerConfigurator->singleton(EntityManagerConfiguratorInterface::class, EntityManagerConfigurator::class);
};
