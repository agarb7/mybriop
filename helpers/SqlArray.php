<?php
namespace app\helpers;

/**
 * SqlArray helper. Convert PostgreSQL arrays to php and vise versa.
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
     * @return string
     */
    public static function encode($array)
    {
        sort($array);
        return '{' . implode(',', $array) . '}';
    }
}
