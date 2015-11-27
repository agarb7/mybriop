<?php

namespace app\enums;


class EtapObrazovaniya extends EnumBase
{
    const DOSHKOLNOE_OBRAZOVANIE = 'dosh';
    const NACHALNOE_OBSCHEE_OBRAZOVANIE = 'no';
    const OSNOVNOE_OBSCHEE_OBRAZOVANIE = 'oo';
    const SREDNEE_OBSCHEE_OBRAZOVANIE = 'so';
    const SREDNEE_PROFESSIONALNOE_OBRAZOVANIE = 'sp';
    const DOPOLNITELNOE_OBRAZOVANIE = 'dop';
    const VYSSHEE_PROFESSIONALNOE_OBRAZOVANIE = 'vp';

    public static function namesMap()
    {
        return [
            self::DOSHKOLNOE_OBRAZOVANIE => 'дошкольное образование',
            self::NACHALNOE_OBSCHEE_OBRAZOVANIE => 'начальное общее образование',
            self::OSNOVNOE_OBSCHEE_OBRAZOVANIE => 'основное общее образование',
            self::SREDNEE_OBSCHEE_OBRAZOVANIE => 'среднее общее образование',
            self::SREDNEE_PROFESSIONALNOE_OBRAZOVANIE => 'среднее профессиональное образование',
            self::DOPOLNITELNOE_OBRAZOVANIE => 'дополнительное образование',
            self::VYSSHEE_PROFESSIONALNOE_OBRAZOVANIE => 'высшее профессиональное образование'
        ];
    }
}
