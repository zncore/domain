<?php

namespace ZnCore\Domain\Helpers;

use Psr\Container\ContainerInterface;
use ZnCore\Base\Helpers\DeprecateHelper;
use ZnCore\Domain\Interfaces\Libs\EntityManagerInterface;

//DeprecateHelper::softThrow();

class EntityManagerHelper
{

    public static function bindEntityManager(ContainerInterface $container, $entitiesConfig): void
    {
        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);
        if (!empty($entitiesConfig['entities'])) {
            foreach ($entitiesConfig['entities'] as $entityClass => $repositoryInterface) {
                $em->bindEntity($entityClass, $repositoryInterface);
            }
        }
        $em->setConfig($entitiesConfig);
    }
}
