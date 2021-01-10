<?php

namespace ZnCore\Domain\Libs;

use Illuminate\Support\Collection;
use Psr\Container\ContainerInterface;
use ZnCore\Domain\Helpers\EntityHelper;

class EntityManager
{

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getRepositoryByClass(string $class): object
    {
        return $this->container->get($class);
    }

    public function createEntity(string $entityClassName, $attributes = []): object
    {
        $entityInstance = $this->container->get($entityClassName);
        if ($attributes) {
            EntityHelper::setAttributes($entityInstance, $attributes);
        }
        return $entityInstance;
    }

    public function createEntityCollection(string $entityClassName, array $items): Collection
    {
        $collection = new Collection();
        foreach ($items as $item) {
            $entityInstance = $this->createEntity($entityClassName, $item);
            $collection->add($entityInstance);
        }
        return $collection;
    }
}
