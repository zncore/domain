<?php

namespace ZnCore\Domain\Entity\Helpers;

use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use ReflectionClass;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use ZnCore\Base\Instance\Helpers\ClassHelper;
use ZnCore\Base\Arr\Helpers\ArrayHelper;
use ZnCore\Base\Text\Helpers\Inflector;
use ZnCore\Base\DynamicEntity\Interfaces\DynamicEntityAttributesInterface;

class CollectionHelper
{

    public static function indexing(Enumerable $collection, string $fieldName): array
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $array = [];
        foreach ($collection as $item) {
            $pkValue = $propertyAccessor->getValue($item, $fieldName);
            $array[$pkValue] = $item;
        }
        return $array;
    }

    public static function create(string $entityClass, array $data = [], array $filedsOnly = []): Enumerable
    {
        foreach ($data as $key => $item) {
            $entity = new $entityClass;
            EntityHelper::setAttributes($entity, $item, $filedsOnly);
            $data[$key] = $entity;
        }
        $collection = new Collection($data);
        return $collection;
    }

    /**
     * @param Enumerable $collection
     * @return array
     */
    public static function toArray(Enumerable $collection): array
    {
        $serializer = new Serializer([new ObjectNormalizer()]);
        $normalizeHandler = function ($value) use ($serializer) {
            return $serializer->normalize($value);
            //return is_object($value) ? EntityHelper::toArray($value) : $value;
        };
        $normalizeCollection = $collection->map($normalizeHandler);
        return $normalizeCollection->all();
    }

    public static function getColumn(Enumerable $collection, string $key): array
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $array = [];
        foreach ($collection as $entity) {
            $array[] = $propertyAccessor->getValue($entity, $key);
        }
        $array = array_values($array);
        return $array;
    }
}
