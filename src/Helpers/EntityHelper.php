<?php

namespace ZnCore\Domain\Helpers;

use Illuminate\Support\Collection;
use ReflectionClass;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use ZnCore\Base\Helpers\ClassHelper;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Base\Legacy\Yii\Helpers\Inflector;
use ZnCore\Base\Libs\DynamicEntity\Interfaces\DynamicEntityAttributesInterface;

class EntityHelper
{

    public static function getValue(object $enitity, string $attribute)
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        return $propertyAccessor->getValue($enitity, $attribute);
    }

    public static function createEntity(string $entityClass, $attributes = [])
    {
        $entityInstance = ClassHelper::createObject($entityClass);
        if ($attributes) {
            self::setAttributes($entityInstance, $attributes);
        }
        return $entityInstance;
    }

    public static function isEntity($data)
    {
        return is_object($data) && !($data instanceof Collection);
    }

    public static function indexingCollection(Collection $collection, string $fieldName): array
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $array = [];
        foreach ($collection as $item) {
            $pkValue = $propertyAccessor->getValue($item, $fieldName);
            $array[$pkValue] = $item;
        }
        return $array;
    }

    public static function createEntityCollection(string $entityClass, array $data = [], array $filedsOnly = []): Collection
    {
        foreach ($data as $key => $item) {
            $entity = new $entityClass;
            self::setAttributes($entity, $item, $filedsOnly);
            $data[$key] = $entity;
        }
        $collection = new Collection($data);
        return $collection;
    }

    public static function toArrayForTablize(object $entity, array $columnList = []): array
    {
        $array = self::toArray($entity);
        $arraySnakeCase = [];
        foreach ($array as $name => $value) {
            $tableizeName = Inflector::underscore($name);
            $arraySnakeCase[$tableizeName] = $value;
        }
        if ($columnList) {
            $arraySnakeCase = ArrayHelper::extractByKeys($arraySnakeCase, $columnList);
        }
        return $arraySnakeCase;
    }

    public static function collectionToArray(Collection $collection): array
    {
        $serializer = new Serializer([new ObjectNormalizer()]);
        $normalizeHandler = function ($value) use ($serializer) {
            return $serializer->normalize($value);
            //return is_object($value) ? EntityHelper::toArray($value) : $value;
        };
        $normalizeCollection = $collection->map($normalizeHandler);
        return $normalizeCollection->all();
    }

    public static function toArray($entity, bool $recursive = false): array
    {
        $array = [];
        if (is_array($entity)) {
            $array = $entity;
        } elseif ($entity instanceof Collection) {
            $array = $entity->toArray();
        } elseif (is_object($entity)) {
            $attributes = self::getAttributeNames($entity);
            if ($attributes) {
                $propertyAccessor = PropertyAccess::createPropertyAccessor();
                foreach ($attributes as $attribute) {
                    $array[$attribute] = $propertyAccessor->getValue($entity, $attribute);
                }
            } else {
                $array = (array)$entity;
            }
        }
        if ($recursive) {
            foreach ($array as $key => $item) {
                if (is_object($item) || is_array($item)) {
                    $array[$key] = self::toArray($item, $recursive/*, $keyFormat*/);
                }
            }
        }
        foreach ($array as $key => $value) {
            $isPrivate = mb_strpos($key, "\x00*\x00") !== false;
            if ($isPrivate) {
                unset($array[$key]);
            }
        }
        return $array;
    }

    public static function getAttributeNames($entity): array
    {
        if ($entity instanceof DynamicEntityAttributesInterface) {
            return $entity->attributes();
        }
        $reflClass = new ReflectionClass($entity);
        $attributesRef = $reflClass->getProperties();
        $attributes = ArrayHelper::getColumn($attributesRef, 'name');
        foreach ($attributes as $index => $attributeName) {
            if ($attributeName[0] == '_') {
                unset($attributes[$index]);
            }
        }
        return $attributes;
    }

    public static function isWritableAttribute(object $entity, string $name): bool
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        return $propertyAccessor->isWritable($entity, $name);
    }

    public static function isReadableAttribute(object $entity, string $name): bool
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        return $propertyAccessor->isReadable($entity, $name);
    }

    public static function setAttribute(object $entity, string $name, $value): void
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $propertyAccessor->setValue($entity, $name, $value);
    }

    public static function setAttributesFromObject(object $fromObject, object $toObject): void
    {
        $entityAttributes = EntityHelper::toArray($fromObject);
        $entityAttributes = ArrayHelper::extractByKeys($entityAttributes, EntityHelper::getAttributeNames($toObject));
        EntityHelper::setAttributes($toObject, $entityAttributes);
    }

    public static function setAttributes(object $entity, $data, array $filedsOnly = []): void
    {
        if (empty($data)) {
            return;
        }
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        foreach ($data as $name => $value) {
            $name = Inflector::variablize($name);
            $isAllow = empty($filedsOnly) || in_array($name, $filedsOnly);
            if ($isAllow) {
                $isWritable = $propertyAccessor->isWritable($entity, $name);
                if ($isWritable) {
                    $propertyAccessor->setValue($entity, $name, $value);
                }
            }
        }
    }

    public static function getAttribute(object $entity, string $key)
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        return $propertyAccessor->getValue($entity, $key);
    }

    public static function getColumn(Collection $collection, string $key): array
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
