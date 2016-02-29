<?php
namespace app\base;

use ReflectionClass;
use yii\helpers\ArrayHelper;

class BaseEnum
{
    /**
     * Returns the fully qualified name of this class.
     * @return string the fully qualified name of this class.
     */
    public static function className()
    {
        return get_called_class();
    }

    public static function items()
    {
        $refl = new ReflectionClass(static::className());
        return array_values($refl->getConstants());
    }

    public static function has($item)
    {
        return in_array($item, static::items());
    }

    public static function names()
    {
        return [];
    }

    public static function shortNames()
    {
        return [];
    }

    public static function getName($item, $default = null, $short = false)
    {
        return ArrayHelper::getValue(
            $short ? static::shortNames() : static::names(),
            $item,
            $default
        );
    }

    public static function getNames($items, $default = null, $short = false)
    {
        return array_map(function ($item) use ($default, $short) {
            return static::getName($item, $default, $short);
        }, $items);
    }
}