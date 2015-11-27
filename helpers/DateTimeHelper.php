<?php
namespace app\helpers;

class DateTimeHelper
{
    /**
     * @param string $str
     * @return \DateTime|null
     */
    public static function create($str)
    {
        return $str
            ? new \DateTime($str)
            : null;
    }

}
