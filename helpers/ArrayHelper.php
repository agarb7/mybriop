<?php
namespace app\helpers;

class ArrayHelper extends \yii\helpers\ArrayHelper
{
    /**
     * @param integer[] $array
     * @return integer[]
     */
    public static function uniqueSort($array)
    {
        $array = array_unique($array);
        sort($array);
        return $array;
    }

    public static function nulloize($array)
    {
        if(!$array)
            return null;
        return $array;
    }

    public static function select($array, $keys)
    {
        $result = [];
        foreach ($keys as $key)
            $result[$key] = $array[$key];
        return $result;
    }
}
