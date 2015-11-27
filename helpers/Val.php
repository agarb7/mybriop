<?php
namespace app\helpers;

use Yii;

class Val
{
    /**
     * @param object|array $obj
     * @param string|int $prop
     * @return mixed
     */
    public static function of($obj, $prop)
    {
        $args = func_get_args();
        $obj = array_shift($args);

        while (!empty($args)) {
            $prop = array_shift($args);

            if (is_object($obj))
                $obj = $obj->$prop;
            elseif (is_array($obj) && array_key_exists($prop, $obj))
                $obj = $obj[$prop];
            else
                return null;
        }

        return $obj;
    }

    public static function format($format, $obj, $prop)
    {
        $args = func_get_args();
        $format = array_shift($args);

        return self::formatArray($format, $args);
    }

    public static function asText($obj, $prop)
    {
        return self::formatArray('text', func_get_args());
    }

    public static function asInteger($obj, $prop)
    {
        return self::formatArray('integer', func_get_args());
    }

    private static function formatArray($format, $args)
    {
        $value = forward_static_call_array('static::of', $args);

        return Yii::$app->formatter->format($value, $format);
    }

}