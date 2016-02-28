<?php
namespace app\enums2;

use app\base\BaseEnum;

class TipKursa extends BaseEnum
{
    const PK = 'pk';
    const PP = 'pp';
    const PO = 'po';

    public static function names()
    {
        return [
            self::PK => 'повышение квалификации',
            self::PP => 'профессиональная переподготовка',
            self::PO => 'профессиональное обучение'
        ];
    }

    public static function shortNames()
    {
        return [
            self::PK => 'ПК',
            self::PP => 'ПП',
            self::PO => 'ПО'
        ];
    }
}