<?php
namespace app\enums;

class TipKursa extends EnumBase
{
    const PK = 'pk';
    const PP = 'pp';
    const PO = 'po';

    public static function namesMap()
    {
        return [
            self::PK => 'повышение квалификации',
            self::PP => 'профессиональная переподготовка',
            self::PO => 'профессиональное обучение'
        ];
    }
}