<?php

namespace ZnCore\Domain\Entity\Helpers;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use ZnCore\Domain\Collection\Libs\Collection;
use ZnCore\Domain\Collection\Interfaces\Enumerable;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class CollectionHelper
{

    public static function where(Enumerable $collection, $field, $operator, $value) {
        $expr = new Comparison($field, $operator, $value);
        $criteria = new Criteria();
        $criteria->andWhere($expr);
        return $collection->matching($criteria);
    }

    public static function merge(Enumerable $collection, Enumerable $source): Enumerable
    {
        $result = clone $collection;
        self::appendCollection($result, $source);
        /*foreach ($source as $item) {
            $result->add($item);
        }*/
        return $result;
    }

    public static function appendCollection(Enumerable $collection, Enumerable $source): void
    {
        foreach ($source as $item) {
            $collection->add($item);
        }
    }

    public static function chunk(Enumerable $collection, $size)
    {
        if ($size <= 0) {
            return new Collection();
        }

        $chunks = [];

        foreach (array_chunk($collection->toArray(), $size, true) as $chunk) {
            $chunks[] = new Collection($chunk);
        }

        return new Collection($chunks);
    }



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
        return $normalizeCollection->toArray();
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
