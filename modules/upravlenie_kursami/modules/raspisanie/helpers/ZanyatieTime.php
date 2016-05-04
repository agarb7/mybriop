<?php
namespace app\upravlenie_kursami\raspisanie\helpers;

/**
 * ZanyatieTime helper for creation time string by number
 */
class ZanyatieTime
{
    private static $_intervals = [
        1 => ['09:00', '10:30'],
        2 => ['10:40', '12:10'],
        3 => ['13:00', '14:30'],
        4 => ['14:40', '16:10'],
        5 => ['16:20', '17:50'],
        6 => ['18:00', '19:30']
    ];

    /**
     * Return lesson time interval by number of row in day's scheduling. E.g. for 0 it is '9:00—10:30'.
     *
     * @param $no
     * @return string|null
     */
    public static function interval($no)
    {
        if (!isset(self::$_intervals[$no]))
            return null;
        
        return self::$_intervals[$no][0] . '—' . self::$_intervals[$no][1];
    }
}