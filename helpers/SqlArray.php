<?php
namespace app\helpers;

use app\base\BaseEnum;
use yii\helpers\ArrayHelper;

/**
 * SqlArray helper. Convert PostgreSQL arrays to php and vise versa.
 * (Deprecated)
 */
class SqlArray
{
    /**
     * Decodes the given PostgreSQL array into a PHP array.
     * @param string $sqlArray
     * @return array
     */
    public static function decode($sqlArray)
    {
        return explode(',', substr($sqlArray, 1, -1));
    }

    /**
     * Encodes the given array into a PostgreSQL array format.
     * @param array $array
     * @param string $sortAs Class name of enum
     * @return string
     */
    public static function encode($array, $sortAs = null)
    {
        if ($sortAs !== null) {
            /* @var $sortAs BaseEnum */
            $order = array_flip($sortAs::items());

            usort($array, function ($l, $r) use ($order) {
                return ArrayHelper::getValue($order, $l, 0) - ArrayHelper::getValue($order, $r, 0);
            });
        }

        return '{' . implode(',', $array) . '}';
    }
}
