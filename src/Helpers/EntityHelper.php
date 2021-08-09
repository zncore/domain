<?php

namespace ZnCore\Domain\Helpers;

use Illuminate\Support\Collection;
use ZnBundle\Eav\Domain\Entities\DynamicEntity;
use ReflectionClass;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use ZnCore\Base\Helpers\ClassHelper;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Base\Legacy\Yii\Helpers\Inflector;
use ZnCore\Domain\Interfaces\Entity\EntityAttributesInterface;

class EntityHelper
{

    public static function getValue(object $enitity, string $attribute) {
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


    /*public static function collectionToArrayForTablize(object $entity, array $columnList = []): array
    {
        foreach ($result['items'] as $entity) {
//            ValidationHelper::validateEntity($entity);
//            $columnList = $this->getColumnsForModify();
            $array[] = EntityHelper::toArrayForTablize($entity);
        }
    }*/

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

    public static function toArray($entity, bool $recursive = false/*, string $keyFormat = null*/): array
    {
        $array = [];
        if (is_array($entity)) {
            $array = $entity;
        } elseif ($entity instanceof Collection) {
            $array = $entity->toArray();
        } elseif (is_object($entity)) {
            $attributes = self::getAttributeNames($entity);
            if($attributes) {
                $propertyAccessor = PropertyAccess::createPropertyAccessor();
                foreach ($attributes as $attribute) {
                    $array[$attribute] = $propertyAccessor->getValue($entity, $attribute);
                }
            } else {
                $array = (array) $entity;
            }
        }
        /*if($keyFormat) {
            $formattedArray = [];
            foreach ($array as $key => $value) {
                $formattedKey = $key;
                if($keyFormat == 'snackCase') {
                    $formattedKey = Inflector::underscore($key);
                } elseif($keyFormat == 'camelCase') {
                    $formattedKey = Inflector::camelize($key);
                } elseif($keyFormat == 'kebabCase') {
                    $formattedKey = Inflector::camel2id($key);
                }
                $formattedArray[$formattedKey] = $value;
            }
            $array = $formattedArray;
        }*/
        if ($recursive) {
            foreach ($array as $key => $item) {
                if (is_object($item) || is_array($item)) {
                    $array[$key] = self::toArray($item, $recursive/*, $keyFormat*/);
                }
            }
        }
        foreach ($array as $key => $value) {
            $isPrivate = mb_strpos($key, "\x00*\x00") !== false;
            if($isPrivate) {
                unset($array[$key]);
            }
        }
        return $array;
    }

    public static function getAttributeNames($entity): array
    {
        if($entity instanceof EntityAttributesInterface) {
            return $entity->attributes();
        }
        $reflClass = new ReflectionClass($entity);
        $attributesRef = $reflClass->getProperties();
        $attributes = ArrayHelper::getColumn($attributesRef, 'name');
        foreach ($attributes as $index => $attributeName) {
            if($attributeName[0] == '_') {
                unset($attributes[$index]);
            }
        }
        return $attributes;
        /*$attributes = [];
        foreach ($attributesRef as $reflectionProperty) {
            $attributes[] = $reflectionProperty->;
        }
        return $attributes;*/
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

    public static function setAttributesFromObject(object $entity, object $object): void
    {
        $entityAttributes = EntityHelper::toArray($entity);
        $entityAttributes = ArrayHelper::extractByKeys($entityAttributes, EntityHelper::getAttributeNames($object));
        EntityHelper::setAttributes($entity, $entityAttributes);
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
                if($isWritable) {
                    $propertyAccessor->setValue($entity, $name, $value);
                }
            }
        }
    }

    /*public static function createEntity(strung $entityClass, array $data = [])
    {
        $entity = new $entityClass;
        self::setAttributes($entity, $data);
        return $entity;
    }*/

    /*public static function getColumn(\Illuminate\Support\Collection $collection, string $columnName) : array {
        $tableArray = self::collectionToArray($tableCollection);
        $tableNameArray = ArrayHelper::getColumn($tableArray, $columnName);
        return $tableNameArray;
    }*/

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

    /*public static function getValue($array, $key, $default = null)
    {
        if ($key instanceof \Closure) {
            return $key($array, $default);
        }

        if (is_array($key)) {
            $lastKey = array_pop($key);
            foreach ($key as $keyPart) {
                $array = static::getValue($array, $keyPart);
            }
            $key = $lastKey;
        }

        if (is_array($array) && (isset($array[$key]) || array_key_exists($key, $array))) {
            return $array[$key];
        }

        if (($pos = strrpos($key, '.')) !== false) {
            $array = static::getValue($array, substr($key, 0, $pos), $default);
            $key = substr($key, $pos + 1);
        }

        if (is_object($array)) {
            // this is expected to fail if the property does not exist, or __get() is not implemented
            // it is not reliably possible to check whether a property is accessible beforehand
            return $array->$key;
        } elseif (is_array($array)) {
            return (isset($array[$key]) || array_key_exists($key, $array)) ? $array[$key] : $default;
        }

        return $default;
    }*/

}