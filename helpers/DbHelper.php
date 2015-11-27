<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 24.04.15
 * Time: 17:26
 */

namespace app\helpers;

class DbHelper
{
    /**
     * @param \DateTime $datetime
     * @return string
     */
    public static function sqlDateTime($datetime)
    {
        return $datetime->format(\DateTime::ISO8601);
    }

//    public static function subqueryColumnsNames($subquery_alias, $columns)
//    {
//        $names = [];
//        foreach ($columns as $alias => $column) {
//            $strip_name = is_string($alias)
//                ? $alias
//                : self::makeColumnName($column);
//            $names[] = "$subquery_alias.$strip_name";
//        }
//        return $names;
//    }
//
//    private static function makeColumnName($column)
//    {
//        return mb_ereg_replace('^(?:\S+\.)?(\S+)$', '\1', $column);
//    }
}