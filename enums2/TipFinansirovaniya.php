<?php
namespace app\enums2;

use app\base\BaseEnum;

class TipFinansirovaniya extends BaseEnum
{
    const BYUDZHET = 'byudzhet';
    const VNEBYUDZHET = 'vnebyudzhet';

    public static function names()
    {
        return [
            self::BYUDZHET => 'бюджет',
            self::VNEBYUDZHET => 'внебюджет'
        ];
    }
}