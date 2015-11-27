<?php
namespace app\enums;

class TipRazdelaKursa extends EnumBase
{
    const BAZOVYJ = 'baz';
    const PROFILNYJ = 'prof';

    public static function namesMap()
    {
        return [
            self::BAZOVYJ => 'базовый',
            self::PROFILNYJ => 'профильный',
        ];
    }
}