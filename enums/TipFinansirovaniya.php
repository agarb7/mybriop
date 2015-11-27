<?php
namespace app\enums;

class TipFinansirovaniya extends EnumBase
{
    const BYUDZHET = 'byudzhet';
    const VNEBYUDZHET = 'vnebyudzhet';

    public static function namesMap()
    {
        return [
            self::BYUDZHET => 'бюджет',
            self::VNEBYUDZHET => 'внебюджет'
        ];
    }
}
