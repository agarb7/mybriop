<?php
namespace app\enums;

use yii\base\Object;
use yii\helpers\ArrayHelper;
use ReflectionClass;
use ReflectionMethod;

class EnumBase extends Object
{
    public static function getName($value)
    {
        return self::getByMap(static::namesMap(), $value);
    }

    public static function getNameBySql($sql)
    {
        return self::getName(self::asValue($sql));
    }

    public static function asValue($sql)
    {
        return self::getByMap(static::valuesMap(), $sql);
    }

    public static function asSql($value)
    {
        return self::getByMap(static::sqlMap(), $value);
    }

    public static function getSqlOrder($sql)
    {
        return self::getByMap(static::sqlOrderMap(), $sql);
    }

    public static function asValuesArray($sql_array)
    {
        $sql_enums = explode(',', substr($sql_array,1,-1));
        $ar = array_map([get_called_class(), 'asValue'], $sql_enums);
        sort($ar);
        return $ar;
    }

    public static function asSqlArray($values_array)
    {
        $ar = array_map([get_called_class(), 'asSql'], $values_array);

        usort($ar, function ($l,$r) {
            return static::getSqlOrder($l) - static::getSqlOrder($r);
        });

        return '{' . implode(',', $ar) . '}';
    }

    public static function getNamesArray($values_array)
    {
        return array_map([get_called_class(), 'getName'], $values_array);
    }

    public static function namesMap()
    {
        return [];
    }

    public static function valuesMap()
    {
        return self::getCachedOrFlipped(self::$_valuesMapCache, 'sqlMap');
    }

    public static function sqlMap()
    {
        return self::getCachedOrFlipped(self::$_sqlMapCache, 'valuesMap');
    }

    public static function sqlOrderMap()
    {
        $map = self::getMapFromCache(self::$_sqlOrderMapCache);
        if ($map)
            return $map;

        //assume that consts returned in declaration order
        $consts = (new ReflectionClass(get_called_class()))->getConstants();
        $map = [];
        $ord = 0;
        foreach ($consts as $value)
            $map[static::asSql($value)] = $ord++;

        self::setMapToCache(self::$_sqlOrderMapCache, $map);
        return $map;
    }

    private static function getByMap($map, $key)
    {
        return $map
            ? ArrayHelper::getValue($map, $key)
            : $key;
    }

    private static function getCachedOrFlipped($cache, $to_flip_getter)
    {
        $map = self::getMapFromCache($cache);
        if ($map)
            return $map;

        if (self::isMethodOverridden($to_flip_getter))
            $map = array_flip(static::$to_flip_getter());

        self::setMapToCache($cache, $map);
        return $map;
    }

    private static function getMapFromCache($cache)
    {
        return ArrayHelper::getValue($cache, get_called_class(), []);
    }

    private static function setMapToCache($cache, $map)
    {
        $cache[get_called_class()] = $map;
    }

    private static function isMethodOverridden($method_name)
    {
        $method = new ReflectionMethod(get_called_class(), $method_name);
        return $method->getDeclaringClass()->getName() !== __CLASS__;
    }

    private static $_sqlOrderMapCache = [];
    private static $_valuesMapCache = [];
    private static $_sqlMapCache = [];
}
