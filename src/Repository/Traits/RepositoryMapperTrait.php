<?php

namespace ZnCore\Domain\Repository\Traits;

use Illuminate\Support\Collection;
use ZnCore\Base\Format\Encoders\ChainEncoder;
use ZnCore\Base\Instance\Helpers\ClassHelper;
use ZnCore\Base\Arr\Helpers\ArrayHelper;
use ZnCore\Base\Text\Helpers\Inflector;
use ZnCore\Domain\Entity\Helpers\EntityHelper;
use ZnCore\Domain\Entity\Interfaces\EntityIdInterface;

trait RepositoryMapperTrait
{

    public function mappers(): array
    {
        return [

        ];
    }

    protected function underscore(array $attributes, array $columnList = [])
    {
        $arraySnakeCase = [];
        foreach ($attributes as $name => $value) {
            $tableizeName = Inflector::underscore($name);
            $arraySnakeCase[$tableizeName] = $value;
        }
        if ($columnList) {
            $arraySnakeCase = ArrayHelper::extractByKeys($arraySnakeCase, $columnList);
        }
        return $arraySnakeCase;
    }

    protected function mapperEncodeEntity(EntityIdInterface $entity): array
    {
        $attributes = EntityHelper::toArray($entity);
        $attributes = $this->underscore($attributes);
        $mappers = $this->mappers();
        if ($mappers) {
            $encoders = new ChainEncoder(new Collection($mappers));
            $attributes = $encoders->encode($attributes);
        }
        $columnList = $this->getColumnsForModify();
        $attributes = ArrayHelper::extractByKeys($attributes, $columnList);
        return $attributes;
    }

    protected function mapperDecodeEntity(array $array): object
    {
        $mappers = $this->mappers();
        if ($mappers) {
            $mappers = array_reverse($mappers);
            $encoders = new ChainEncoder(new Collection($mappers));
            $array = $encoders->decode($array);
        }
        $entity = ClassHelper::createInstance($this->getEntityClass());
        EntityHelper::setAttributes($entity, $array);
        return $entity;
    }

    protected function mapperDecodeCollection(array $array): Collection
    {
        $collection = new Collection();
        foreach ($array as $item) {
            $entity = $this->mapperDecodeEntity((array)$item);
            $collection->add($entity);
        }
        return $collection;
    }
}
