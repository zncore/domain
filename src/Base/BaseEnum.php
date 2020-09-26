<?php

namespace ZnCore\Domain\Base;

use ZnCore\Base\Exceptions\DeprecatedException;
use ZnCore\Base\Helpers\ReflectionHelper;

throw new DeprecatedException;

/**
 * Class BaseEnum
 * @package ZnCore\Domain\Base
 * @deprecated
 */
abstract class BaseEnum
{

    public static function getLabel($constValue) {
        $labels = static::getLabels();
        return $labels[$constValue];
    }

    public static function getLabels() {
        return [];
    }

    public static function values($prefix = null)
    {
        $constants = static::all($prefix);
        $constants = array_values($constants);
        $constants = array_unique($constants);
        return $constants;
    }

    public static function keys($prefix = null)
    {
        $constants = static::all($prefix);
        $constants = array_keys($constants);
        return $constants;
    }

    public static function all($prefix = null)
    {
        $className = get_called_class();
        if ( ! empty($prefix)) {
            $constants = ReflectionHelper::getConstantsByPrefix($className, $prefix);
        } else {
            $constants = ReflectionHelper::getConstants($className);
        }
        return $constants;
    }

    public static function validate($value, $prefix = null)
    {
        if ( ! self::isValid($value, $prefix)) {
            $class = static::class;
            throw new InvalidArgumentException("Value \"$value\" not contains in \"$class\" enum");
        }
    }

    public static function isValid($value, $prefix = null)
    {
        return in_array($value, static::values($prefix));
    }

    public static function isValidSet($values, $prefix = null)
    {
        foreach ($values as $value) {
            if ( ! self::isValid($value, $prefix)) {
                return false;
            }
        }
        return true;
    }

    public static function value($value, $default = null, $prefix = null)
    {
        if (self::isValid($value, $prefix)) {
            return $value;
        } else {
            if ($default !== null && self::isValid($default, $prefix)) {
                return $default;
            }
            $values = self::values($prefix);
            return $values[0];
        }
    }

    public static function keyByValue($value, $prefix = null)
    {
        $all = self::all($prefix);
        $allFliped = array_flip($all);
        return ArrayHelper::getValue($allFliped, $value);
    }

    public static function getValue($key, $default = null, $prefix = null)
    {
        $all = self::all($prefix);
        return ArrayHelper::getValue($all, $key, $default);
    }

}
