<?php
namespace app\helpers;

use DateTime;

use yii\helpers\ArrayHelper;

use app\base\BaseEnum;

/**
 * Sqltype helper. Convert sql types to php and vise versa.
 */
class SqlType
{
    /**
     * Decodes the given SQL array-set into a PHP array.
     * @param string $sqlArray
     * @return array
     */
    public static function decodeArraySet($sqlArray)
    {
        return explode(',', substr($sqlArray, 1, -1));
    }

    /**
     * Encodes the given PHP array into a SQL array-set
     * @param array $array
     * @param string $sortAs Class name of enum
     * @return string
     */
    public static function encodeArraySet($array, $sortAs = null)
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

    /**
     * @param DateTime|string $date
     * @return string
     */
    public static function encodeDate($date)
    {
        return self::ensureDateTime($date)->format('Y-m-d'); 
    }

    /**
     * @param DateTime|string $date
     * @return DateTime
     */
    private static function ensureDateTime($date)
    {
        if ($date instanceof DateTime)
            return $date;
        
        return new DateTime($date);
    }


}